// --- DOM Elements ---
const login = document.getElementById("login");
login.disabled = true;
const playButton = document.getElementById("playButton");
const gameModalElement = document.getElementById("gameModal");
const gameBoard = document.getElementById("gameBoard");
const gameCells = document.querySelectorAll(".cell");
const gameStatus = document.getElementById("gameStatus");
const restartButton = document.getElementById("restartButton");
const gameModal = new bootstrap.Modal(gameModalElement); // Bootstrap Modal instance

// --- Game Variables ---
let board = [];
let currentPlayer = "X";
let gameActive = true;
let lastWinner = null; // Track winner across games for button color
const winningConditions = [
	[0, 1, 2],
	[3, 4, 5],
	[6, 7, 8], // Rows
	[0, 3, 6],
	[1, 4, 7],
	[2, 5, 8], // Columns
	[0, 4, 8],
	[2, 4, 6], // Diagonals
];

// --- Functions ---

function initializeGame() {
	board = ["", "", "", "", "", "", "", "", ""];
	currentPlayer = "X";
	gameActive = true;
	lastWinner = null; // Reset winner tracking for this game

	gameStatus.textContent = "Your turn (X)";
	gameCells.forEach((cell) => {
		cell.innerHTML = ""; // Clear previous content (spans)
		cell.disabled = false;
		cell.classList.remove("cell-x", "cell-o");
	});

	// Reset main play button if needed (though modal opening implies starting)
	updatePlayButtonState();

	console.log("Game Initialized");
}

function updateBoardUI() {
	gameCells.forEach((cell, index) => {
		if (board[index] && cell.innerHTML === "") {
			// Only update if empty in DOM
			const span = document.createElement("span");
			span.textContent = board[index];
			cell.innerHTML = ""; // Clear potential previous content
			cell.appendChild(span);
			cell.classList.add(board[index] === "X" ? "cell-x" : "cell-o");
			cell.disabled = true; // Disable after move
		} else if (!board[index]) {
			// Ensure cells that might have been reset are clear
			cell.innerHTML = "";
			cell.classList.remove("cell-x", "cell-o");
			cell.disabled = !gameActive; // Only enable if game is active
		} else {
			// Cell already has content and matches board state, ensure disabled
			cell.disabled = true;
		}
	});
}

function checkGameStatus() {
	let roundWon = false;
	let winner = null;

	for (let i = 0; i < winningConditions.length; i++) {
		const [a, b, c] = winningConditions[i];
		if (board[a] && board[a] === board[b] && board[a] === board[c]) {
			roundWon = true;
			winner = board[a];
			break;
		}
	}

	if (roundWon) {
		gameActive = false;
		lastWinner = winner; // Store winner for button color
		gameStatus.textContent = `Player ${winner} Wins!`;
		disableAllCells();
		updatePlayButtonState(); // Update button immediately on win/loss
		console.log(`Winner: ${winner}`);
		return true; // Game ended
	}

	// Check for Draw
	if (!board.includes("")) {
		gameActive = false;
		lastWinner = "Draw"; // Store Draw state
		gameStatus.textContent = "It's a Draw!";
		disableAllCells();
		updatePlayButtonState(); // Update button immediately on draw
		console.log("Draw");
		return true; // Game ended
	}

	return false; // Game continues
}

function disableAllCells() {
	gameCells.forEach((cell) => (cell.disabled = true));
}

function handlePlayerMove(clickedCell, index) {
	if (board[index] !== "" || !gameActive || currentPlayer !== "X") {
		return; // Ignore click if cell taken, game over, or not player's turn
	}

	board[index] = currentPlayer;
	updateBoardUI(); // Update immediately after player move

	if (!checkGameStatus()) {
		// Check if player's move ended the game
		// Game continues, switch to AI
		currentPlayer = "O";
		gameStatus.textContent = "AI is thinking...";
		// Disable board during AI 'thinking'
		gameCells.forEach((cell) => {
			if (!board[cell.dataset.index]) cell.disabled = true;
		});
		setTimeout(triggerAIMove, 500); // AI moves after a short delay
	}
}

// AI Move Logic
function handleAIMove() {
	// Simple AI: chooses first available spot
	// (Can integrate smarter AI logic here if needed)
	let availableSpots = [];
	for (let i = 0; i < board.length; i++) {
		if (board[i] === "") {
			availableSpots.push(i);
		}
	}

	if (availableSpots.length > 0 && gameActive) {
		// --- Optional: Add smarter AI logic here (check win/block) ---

		// Default: pick first available
		const aiMoveIndex = availableSpots[0];
		board[aiMoveIndex] = "O"; // Update logical board
		return aiMoveIndex; // Return the index AI chose
	}
	return -1; // Indicate no move was made
}

// Trigger and Process AI Move
function triggerAIMove() {
	if (!gameActive) return;

	const aiMoveIndex = handleAIMove();

	if (aiMoveIndex !== -1) {
		updateBoardUI(); // Update UI with AI's move

		if (!checkGameStatus()) {
			// Check if AI's move ended the game
			// Game continues, switch back to Player
			currentPlayer = "X";
			gameStatus.textContent = "Your turn (X)";
			// Re-enable only empty cells
			gameCells.forEach((cell) => {
				if (!board[cell.dataset.index]) {
					cell.disabled = false;
				}
			});
		}
	} else {
		// This case should only happen if the board is full but checkGameStatus didn't declare Draw yet
		console.log("AI couldn't find a spot - should be Draw?");
		checkGameStatus(); // Re-check just in case
	}
}

// Update main play button color and text based on last game's outcome
function updatePlayButtonState() {
	if (lastWinner === "X") {
		playButton.classList.remove("btn-danger");
		playButton.classList.add("btn-success");
		login.disabled = false;
		restartButton.disabled = true;
		playButton.disabled = true;

		playButton.textContent = "Not a robot";
	} else {
		playButton.classList.remove("btn-success");
		playButton.classList.add("btn-danger");
		playButton.textContent = "Check if you are a robot";
	}
}

// --- Event Listeners ---

// Handle cell clicks using event delegation on the board
gameBoard.addEventListener("click", (event) => {
	const clickedCell = event.target;
	// Ensure the clicked element is a cell button and not the board itself
	if (clickedCell.classList.contains("cell")) {
		const index = parseInt(clickedCell.dataset.index);
		handlePlayerMove(clickedCell, index);
	}
});

// Restart game when restart button inside modal is clicked
restartButton.addEventListener("click", initializeGame);

// Initialize game when modal is shown (or about to be shown)
gameModalElement.addEventListener("show.bs.modal", initializeGame);

// Optional: Update the main button state when the modal is fully hidden
// gameModalElement.addEventListener('hidden.bs.modal', updatePlayButtonState);
// Note: We are now updating the button immediately when the game ends.
