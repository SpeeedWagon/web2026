<?php

// Define constants for cookie name and expiry
define('REMEMBER_ME_COOKIE_NAME', 'user_auth_token');
define('REMEMBER_ME_EXPIRY_SECONDS', 86400 * 30); // 30 days expiry

/**
 * Creates a new persistent login token, stores it in DB, and sets the cookie.
 */
function createRememberMeToken(PDO $pdo, int $userId): void
{
    // Clear any old tokens for this user first for hygiene
    clearUserTokens($pdo, $userId);

    $selector = bin2hex(random_bytes(16)); // 32 chars hex
    $validator = bin2hex(random_bytes(32)); // 64 chars hex
    $validatorHash = hash('sha256', $validator); // Hash the validator for DB storage
    $expires = date('Y-m-d H:i:s', time() + REMEMBER_ME_EXPIRY_SECONDS);

    try {
        $stmt = $pdo->prepare("INSERT INTO persistent_logins (user_id, selector, validator_hash, expires) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $selector, $validatorHash, $expires]);

        // Set the cookie: "selector:validator"
        $cookieValue = $selector . ':' . $validator;
        setcookie(
            REMEMBER_ME_COOKIE_NAME,
            $cookieValue,
            time() + REMEMBER_ME_EXPIRY_SECONDS,
            '/',                     // Path (available site-wide)
            '',                      // Domain (current domain)
            isset($_SERVER["HTTPS"]), // Secure flag (set if HTTPS)
            true                     // HttpOnly flag (critical!)
        );

    } catch (PDOException $e) {
        // Log error, don't expose details to user
        error_log("Error creating remember me token for user {$userId}: " . $e->getMessage());
        // Optionally inform user "Remember me failed" but login might still be ok
    }
}

/**
 * Validates the remember me cookie, logs in user if valid, and cycles the token.
 * Returns true if login via cookie was successful, false otherwise.
 */
function loginWithRememberMeCookie(PDO $pdo): bool
{
    error_log("DEBUG: loginWithRememberMeCookie START"); // Keep for debugging if needed

    // Check if cookie exists
    if (!isset($_COOKIE[REMEMBER_ME_COOKIE_NAME])) {
        error_log("DEBUG: Cookie '" . REMEMBER_ME_COOKIE_NAME . "' not set.");
        return false;
    }

    $cookieValue = $_COOKIE[REMEMBER_ME_COOKIE_NAME];
    // Basic check for expected format
    if (strpos($cookieValue, ':') === false) {
        error_log("DEBUG: Cookie value invalid format (no ':').");
        clearRememberMeCookie();
        return false;
    }

    list($selector, $validator) = explode(':', $cookieValue, 2);
    error_log("DEBUG: Extracted Selector: {$selector}, Validator: {$validator}"); // Keep for debugging if needed

    if (empty($selector) || empty($validator)) {
        error_log("DEBUG: Selector or Validator is empty after explode.");
        clearRememberMeCookie(); // Invalid format
        return false;
    }

    try {
        // Find the token by selector, ensuring it hasn't expired
        $stmt = $pdo->prepare("SELECT * FROM persistent_logins WHERE selector = ? AND expires >= NOW()");
        $stmt->execute([$selector]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tokenData) {
            error_log("DEBUG: DB Token Found: " . print_r($tokenData, true)); // Keep for debugging if needed
            // Found token, now verify the validator hash
            $validatorHashFromCookie = hash('sha256', $validator);

            // Use hash_equals for timing-attack-safe comparison!
            if (hash_equals($tokenData['validator_hash'], $validatorHashFromCookie)) {
                // --- Token is valid! ---
                error_log("DEBUG: Hashes MATCH!");

                // ===>>> STEP 1: Get the User ID from the token data <<<===
                $userId = $tokenData['user_id'];

                // ===>>> STEP 2: Fetch the username from the users table <<<===
                $username = 'hhsas'; // Default value in case fetch fails
                try {
                    $userStmt = $pdo->prepare("SELECT user_name FROM users WHERE id = ?");
                    $userStmt->execute([$userId]);
                    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && isset($user['user_name'])) {
                        $username = $user['user_name']; // Successfully fetched username
                        error_log("DEBUG: Fetched username '{$username}' for user_id {$userId}");
                    } else {
                        // This case is odd: valid token but user doesn't exist?
                        // Could happen if user deleted between token creation and use.
                        // Log this inconsistency.
                        error_log("WARNING: Valid token found for user_id {$userId}, but user could not be found in users table.");
                        // Decide if login should fail here. For now, we proceed but log it.
                        // Optionally: Invalidate the token here since the user doesn't seem valid anymore.
                        // $deleteStmt = $pdo->prepare("DELETE FROM persistent_logins WHERE id = ?");
                        // $deleteStmt->execute([$tokenData['id']]);
                        // clearRememberMeCookie();
                        // return false; // Fail the login
                    }
                } catch (PDOException $e) {
                    error_log("ERROR fetching username for user_id {$userId}: " . $e->getMessage());
                    // Proceed with default username or handle as a critical failure?
                    // Depending on app needs, might want to `return false;` here.
                }
                // ===>>> END Fetch username <<<===


                // ===>>> STEP 3: Start session and store user ID AND username <<<===
                // session_regenerate_id(true); // Regenerate session ID
                $_SESSION['user_id'] = $userId; // Store the user ID
                $_SESSION['username'] = $username; // Store the fetched username
                $_SESSION['logged_in_timestamp'] = time();
                error_log("DEBUG: Session started/regenerated. user_id: {$userId}, username: {$username}");


                // --- Security Enhancement: Cycle the token ---
                $newValidator = bin2hex(random_bytes(32));
                $newValidatorHash = hash('sha256', $newValidator);
                $updateStmt = $pdo->prepare("UPDATE persistent_logins SET validator_hash = ? WHERE id = ?");
                $updateStmt->execute([$newValidatorHash, $tokenData['id']]);
                error_log("DEBUG: Token cycled in DB for id {$tokenData['id']}");

                // Set the new cookie with the same selector but new validator
                $newCookieValue = $selector . ':' . $newValidator;
                setcookie(
                    REMEMBER_ME_COOKIE_NAME,
                    $newCookieValue,
                    time() + REMEMBER_ME_EXPIRY_SECONDS, // Use same expiry duration
                    '/',
                    '',
                    isset($_SERVER["HTTPS"]),
                    true
                );
                error_log("DEBUG: New cookie set: {$newCookieValue}");

                error_log("DEBUG: loginWithRememberMeCookie RETURNING TRUE");
                return true; // Login successful

            } else {
                // Validator mismatch - potential tampering or stolen cookie reuse
                error_log("DEBUG: Hashes DO NOT MATCH! Deleting token.");
                // Delete the compromised token from DB
                $deleteStmt = $pdo->prepare("DELETE FROM persistent_logins WHERE id = ?");
                $deleteStmt->execute([$tokenData['id']]);
                clearRememberMeCookie(); // Clear the bad cookie
                error_log("DEBUG: loginWithRememberMeCookie RETURNING FALSE (hash mismatch)");
                return false;
            }
        } else {
            // Selector not found or token expired
            error_log("DEBUG: DB Token NOT Found or expired for selector {$selector}. Clearing cookie.");
            clearRememberMeCookie(); // Clear the invalid/expired cookie
            error_log("DEBUG: loginWithRememberMeCookie RETURNING FALSE (token not found/expired)");
            return false;
        }
    } catch (PDOException $e) {
        error_log("ERROR in loginWithRememberMeCookie DB operation: " . $e->getMessage());
        clearRememberMeCookie(); // Clear cookie on DB error
        error_log("DEBUG: loginWithRememberMeCookie RETURNING FALSE (DB exception)");
        return false; // Return false on DB error
    }
}

/**
 * Clears the remember me cookie by setting its expiry in the past.
 */
function clearRememberMeCookie(): void
{
    // Check if cookie exists before trying to unset/delete
    if (isset($_COOKIE[REMEMBER_ME_COOKIE_NAME])) {
        setcookie(
            REMEMBER_ME_COOKIE_NAME,
            '',                     // Empty value
            time() - 3600,          // Expire one hour ago
            '/',
            '',
            isset($_SERVER["HTTPS"]),
            true
        );
        // Also remove from current script's $_COOKIE array
        unset($_COOKIE[REMEMBER_ME_COOKIE_NAME]);
    }
}

/**
 * Deletes all persistent login tokens for a specific user from the database.
 * Useful on logout or if "Remember Me" is unchecked during login.
 */
function clearUserTokens(PDO $pdo, int $userId): void
{
    try {
        $stmt = $pdo->prepare("DELETE FROM persistent_logins WHERE user_id = ?");
        $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Error clearing tokens for user {$userId}: " . $e->getMessage());
    }
}

?>