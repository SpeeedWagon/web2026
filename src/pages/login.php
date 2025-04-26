<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
             <div class="card-body p-4">
                <h2 class="card-title text-center text-primary mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>

                <?php if (!empty($loginError)): // Display login errors if they exist ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                         <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                         <label class="form-check-label" for="remember_me">Remember me</label>
                    </div> 
                    <button id="playButton" class="btn btn-lg btn-danger shadow-sm">
        Play Tic-Tac-Toe (in Alert)
    </button>

                    <div class="d-grid"> <!-- Makes button full width -->
                         <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                   

                </form>
                
                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a  href="index.php?page=register">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
        const playButton = document.getElementById('playButton');

        // --- Game Variables ---
        let board = [];
        let currentPlayer = 'X';
        let gameActive = true;
        const winningConditions = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
            [0, 4, 8], [2, 4, 6]             // Diagonals
        ];

        // --- Functions ---

        function initializeBoard() {
            board = ['', '', '', '', '', '', '', '', ''];
            currentPlayer = 'X';
            gameActive = true;
        }

        function formatBoardForAlert() {
            let boardString = "Tic-Tac-Toe! You are X\n\n";
            boardString += ` ${board[0] || '1'} | ${board[1] || '2'} | ${board[2] || '3'} \n`;
            boardString += `---|---|---\n`;
            boardString += ` ${board[3] || '4'} | ${board[4] || '5'} | ${board[5] || '6'} \n`;
            boardString += `---|---|---\n`;
            boardString += ` ${board[6] || '7'} | ${board[7] || '8'} | ${board[8] || '9'} \n\n`;
            return boardString;
        }

        function checkWinner() {
            for (let i = 0; i < winningConditions.length; i++) {
                const [a, b, c] = winningConditions[i];
                if (board[a] && board[a] === board[b] && board[a] === board[c]) {
                    return board[a]; // 'X' or 'O'
                }
            }
            if (!board.includes('')) {
                return 'Draw';
            }
            return null;
        }

        function handlePlayerMove(squareIndex) {
            if (board[squareIndex] === '' && gameActive) {
                board[squareIndex] = currentPlayer;
                return true;
            }
            return false;
        }

        // Basic AI Move
        function handleAIMove() {
             if (!gameActive) return;

            let availableSpots = [];
            for (let i = 0; i < board.length; i++) {
                if (board[i] === '') {
                    availableSpots.push(i);
                }
            }

            if (availableSpots.length > 0) {
                // Simple AI: chooses first available spot
                // (You can re-add the smarter AI checks here if desired)
                const aiMoveIndex = availableSpots[0];
                board[aiMoveIndex] = 'O';
                 alert(`AI (O) plays square ${aiMoveIndex + 1}`);
            }
        }


        function gameLoop() {
            initializeBoard();
            let finalWinner = null; // Variable to store the winner of this specific game run

            while (gameActive) {
                let boardDisplay = formatBoardForAlert();
                let winnerCheck = checkWinner(); // Check before player's turn

                if (winnerCheck) {
                    gameActive = false;
                    finalWinner = winnerCheck; // Store the winner
                    if (winnerCheck === 'Draw') {
                        alert(boardDisplay + "It's a Draw!");
                    } else {
                        // This case usually happens if O won on the last turn
                        alert(boardDisplay + `Player ${winnerCheck} Wins!`);
                    }
                    break; // Exit loop
                }

                // --- Player's Turn (X) ---
                if (currentPlayer === 'X') {
                    let validInput = false;
                    while (!validInput) {
                        const moveInput = prompt(boardDisplay + "Your turn (X). Enter square number (1-9):");
                        if (moveInput === null) {
                           alert("Game aborted.");
                           gameActive = false;
                           finalWinner = null; // No winner if aborted
                           break;
                        }
                        const move = parseInt(moveInput);
                        if (!isNaN(move) && move >= 1 && move <= 9) {
                            const index = move - 1;
                            if (handlePlayerMove(index)) {
                                validInput = true;
                            } else {
                                alert("Invalid move: Square already taken or game over. Try again.");
                                boardDisplay = formatBoardForAlert();
                            }
                        } else {
                            alert("Invalid input: Please enter a number between 1 and 9.");
                        }
                    }
                    if (!gameActive) break; // Exit outer loop if game aborted
                }
                // --- AI's Turn (O) ---
                else {
                    handleAIMove();
                }

                // Check for winner *after* the current move
                winnerCheck = checkWinner();
                if (winnerCheck) {
                    gameActive = false;
                    finalWinner = winnerCheck; // Store the winner
                    boardDisplay = formatBoardForAlert(); // Get final board state
                    if (winnerCheck === 'Draw') {
                        alert(boardDisplay + "It's a Draw!");
                    } else {
                        alert(boardDisplay + `Player ${winnerCheck} Wins!`);
                    }
                    break; // Exit loop
                }

                // Switch player
                currentPlayer = (currentPlayer === 'X') ? 'O' : 'X';
            } // End while(gameActive)

            // --- Game Over Alert and Button Color Change ---
            alert("Game Over. Click the button to play again!");

            // Change button color based on the final winner of this game
            if (finalWinner === 'X') { // Player (X) won
                 playButton.classList.remove('btn-danger');
                 playButton.classList.add('btn-success');
                 playButton.textContent = 'You Won! Play Again?'; // Optional: Change text
            } else { // Player lost ('O' won) or drew, or aborted
                 playButton.classList.remove('btn-success');
                 playButton.classList.add('btn-danger');
                 playButton.textContent = 'Play Tic-Tac-Toe (in Alert)'; // Reset text
            }
        }

        // --- Event Listener ---
        playButton.addEventListener('click', () => {
             // **Reset button to Red and initial text when starting a new game**
             playButton.classList.remove('btn-success');
             playButton.classList.add('btn-danger');
             playButton.textContent = 'Play Tic-Tac-Toe (in Alert)'; // Reset text

             // Start the game
             gameLoop();
        });

    </script>
