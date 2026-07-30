<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PhotoEditorController extends Controller
{
    /**
     * Rotate candidate profile photo using GD.
     */
    public function rotate(Request $request)
    {
        $request->validate([
            'direction' => 'required|in:left,right',
        ]);

        $user = Auth::user();

        if (empty($user->profile_photo) || !Str::startsWith($user->profile_photo, 'data:image')) {
            return response()->json(['success' => false, 'message' => 'No editable profile photo found.'], 400);
        }

        try {
            // 1. Extract base64 image data
            list($type, $data) = explode(';', $user->profile_photo);
            list(, $data) = explode(',', $data);
            $mime = str_replace('data:', '', $type);
            $binaryData = base64_decode($data);

            // 2. Create image resource from binary data
            $srcImage = imagecreatefromstring($binaryData);
            if (!$srcImage) {
                return response()->json(['success' => false, 'message' => 'Invalid image format.'], 400);
            }

            // 3. Define rotation angle
            $angle = $request->direction === 'right' ? 270 : 90; // gd imagerotate is counter-clockwise

            // 4. Rotate image
            $rotatedImage = imagerotate($srcImage, $angle, 0);
            
            // 5. Capture rotated image buffer
            ob_start();
            if ($mime === 'image/png') {
                imagepng($rotatedImage);
            } else {
                imagejpeg($rotatedImage, null, 90);
            }
            $rotatedBinary = ob_get_clean();

            // Clean up resources
            imagedestroy($srcImage);
            imagedestroy($rotatedImage);

            // 6. Encode back to base64
            $newBase64 = 'data:' . $mime . ';base64,' . base64_encode($rotatedBinary);

            // 7. Save to database
            $user->update([
                'profile_photo' => $newBase64
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo rotated successfully.',
                'new_photo' => $newBase64
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rotation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
