<?php
// captcha_image.php
// session_start();

// // --- Configuration ---
// $length = 5; // Number of characters in the CAPTCHA code
// $width = 120;
// $height = 40;
// $font_size = 20;
// $font_path = __DIR__ . '/fonts/opensans.ttf'; // *** IMPORTANT: Set correct path to your TTF font file ***
//                                            // Download Open Sans or similar, create a 'fonts' folder
//                                            // If no TTF, comment this out and uncomment imagestring below.

// $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
// // --- End Configuration ---


// // --- Generate Random Code ---
// $captcha_code = '';
// $characters_length = strlen($characters);
// for ($i = 0; $i < $length; $i++) {
//     $captcha_code .= $characters[rand(0, $characters_length - 1)];
// }

// // --- Store code in session (case-insensitive comparison later) ---
// $_SESSION['captcha_code'] = strtolower($captcha_code);


// // --- Create Image ---
// $image = imagecreatetruecolor($width, $height);
// if (!$image) { die("GD image create failed"); } // Check if image creation succeeded

// // --- Allocate Colors ---
// $bg_color = imagecolorallocate($image, 230, 230, 230); // Light gray background
// $text_color = imagecolorallocate($image, 50, 50, 50);   // Dark gray text
// $noise_color1 = imagecolorallocate($image, 180, 180, 180); // Noise color 1
// $noise_color2 = imagecolorallocate($image, 200, 200, 200); // Noise color 2

// // --- Fill Background ---
// imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// // --- Add Noise (Lines) ---
// for ($i = 0; $i < 5; $i++) {
//     imageline($image, 0, rand(0, $height), $width, rand(0, $height), $noise_color1);
//     imageline($image, rand(0, $width), 0, rand(0, $width), $height, $noise_color2);
// }

// // --- Add Noise (Dots/Pixels) ---
// for ($i = 0; $i < 500; $i++) {
//     imagesetpixel($image, rand(0, $width), rand(0, $height), $noise_color1);
// }


// // --- Draw Text ---
// if (file_exists($font_path)) {
//     // Use TTF Font (Recommended)
//     $text_box = imagettfbbox($font_size, 0, $font_path, $captcha_code);
//     if ($text_box) {
//         $text_width = $text_box[2] - $text_box[0];
//         $text_height = $text_box[1] - $text_box[7]; // Correct height calculation
//         $x = ($width - $text_width) / 2;
//         $y = ($height + $text_height) / 2; // Center vertically
//         imagettftext($image, $font_size, rand(-5, 5), (int)$x, (int)$y, $text_color, $font_path, $captcha_code);
//     } else {
//          // Fallback if imagettfbbox fails
//          imagestring($image, 5, 10, 10, $captcha_code, $text_color); // Use built-in font
//     }
// } else {
//     // Fallback to built-in font if TTF file not found
//     // Note: Built-in fonts are numbered 1-5. Size calculation is different.
//     $font_num = 5;
//     $text_width = imagefontwidth($font_num) * $length;
//     $x = ($width - $text_width) / 2;
//     $y = ($height - imagefontheight($font_num)) / 2;
//     imagestring($image, $font_num, (int)$x, (int)$y, $captcha_code, $text_color);
// }


$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 255, 0, 0); // Red background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
imagepng($image);
imagedestroy($image);
exit;

// Stop script after sending image
?>