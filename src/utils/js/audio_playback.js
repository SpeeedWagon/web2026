document.addEventListener('DOMContentLoaded', function() { // Ensures DOM is ready

    const players = document.querySelectorAll('.js-audio-player'); // Select all player elements
    const playbackClass = 'is-playing'; // Store class name

    if (!players || players.length === 0) {
        console.warn("No elements found with class 'js-audio-player'.");
        return; // Exit if no players found
    }

    // Iterate through each player element found
    players.forEach((player, index) => {
        const id = 'audio-player-' + index; // Unique ID for reference
        const audioElement = player.querySelector('audio'); // Find audio element within this player
        const videoElement = player.querySelector('video'); // Find video element within this player
        const controlButton = player.querySelector('.js-control'); // Find control button

        // Check if essential elements exist within this player
        if (!audioElement || !controlButton) {
            console.warn("Player #" + index + " is missing audio or control button.", player);
            return; // Skip this player if incomplete (like 'continue' in a loop)
        }

        player.setAttribute('id', id); // Assign the unique ID to the player div

        // --- Event Listener for the Control Button ---
        controlButton.addEventListener('click', () => {
            // 1. Stop any *other* players that might be playing
            stopOtherPlayers(id);

            // 2. Toggle playback for *this* player
            togglePlayback(player, audioElement, videoElement);
        });

        // --- Event Listener for when audio naturally finishes ---
        audioElement.addEventListener('ended', () => {
            console.log('Audio ended for:', id);
            // Reset only this player's state
            player.classList.remove(playbackClass);
            if (videoElement && !videoElement.paused) {
                try {
                    videoElement.pause();
                    // Optional: Reset video to the beginning if desired
                    // videoElement.currentTime = 0;
                } catch (e) {
                    console.error("Error pausing video on end:", e);
                }
            }
        });

        // --- Optional: Handle potential errors during loading/playback ---
        audioElement.addEventListener('error', (e) => {
            console.error("Audio Error on player " + id + ":", e);
            player.classList.remove(playbackClass); // Ensure class is removed on error
            if (videoElement && !videoElement.paused) videoElement.pause();
        });

        if (videoElement) {
            videoElement.addEventListener('error', (e) => {
                console.error("Video Error on player " + id + ":", e);
                // May not need to remove class here if audio is the primary control
            });
        }

    }); // End players.forEach

    // --- Helper Function: Toggles Play/Pause for a specific player ---
    function togglePlayback(playerElement, audio, video) {
        if (audio.paused) {
            // --- Play ---
            try {
                // Browsers often return a Promise from play()
                const playPromise = audio.play();

                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        // Playback started successfully
                        audio.volume = 1; // Set volume instantly
                        playerElement.classList.add(playbackClass);
                        if (video) {
                            const videoPromise = video.play(); // Also play video
                            if (videoPromise !== undefined) {
                                videoPromise.catch(error => console.error(`Video play failed for ${playerElement.id}:`, error));
                            }
                        }
                    }).catch(error => {
                        console.error(`Audio play failed for ${playerElement.id}:`, error);
                        // Ensure UI reflects failure
                        playerElement.classList.remove(playbackClass);
                        if (video && !video.paused) video.pause();
                    });
                } else {
                    // Fallback for older browsers maybe? (Though Promise is standard now)
                    audio.volume = 1;
                    playerElement.classList.add(playbackClass);
                    if (video) video.play();
                }

            } catch (e) {
                console.error(`Error initiating audio playback for ${playerElement.id}:`, e);
                playerElement.classList.remove(playbackClass); // Reset class on error
            }

        } else {
            // --- Pause ---
            try {
                audio.pause();
                // No fade out - pauses instantly
                if (video) video.pause();
            } catch (e) {
                console.error(`Error pausing media for ${playerElement.id}:`, e);
            } finally {
                // Ensure class is removed even if pause throws error
                playerElement.classList.remove(playbackClass);
            }
        }
    }

    // --- Helper Function: Stops all players *except* the one with 'currentPlayerId' ---
    function stopOtherPlayers(currentPlayerId) {
        players.forEach(otherPlayer => { // Iterate through the NodeList of all players
            if (otherPlayer.getAttribute('id') !== currentPlayerId) {
                const otherAudio = otherPlayer.querySelector('audio');
                const otherVideo = otherPlayer.querySelector('video');

                if (otherAudio && !otherAudio.paused) {
                    try {
                        otherAudio.pause();
                        // No fade out - pauses instantly
                    } catch (e) {
                        console.error(`Error pausing other audio (${otherPlayer.id}):`, e);
                    }
                }
                if (otherVideo && !otherVideo.paused) {
                    try {
                        otherVideo.pause();
                    } catch (e) {
                        console.error(`Error pausing other video (${otherPlayer.id}):`, e);
                    }
                }
                // Always remove class from other players if they weren't the current one
                otherPlayer.classList.remove(playbackClass);
            }
        });
    }

}); // End DOMContentLoaded listener