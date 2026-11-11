<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Supporting Auth
use Illuminate\Support\Facades\Auth;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout(); //

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // return redirect('/');
        return redirect('/login');
    }

    public function AdminProfile() {
        $id = Auth::user()->id; // 'Auth' is a default middleware provided by Laravel Breeze which checks whether the user is logged in or not.
        // Throughout this Auth we want to access the user, i.e, our users table as user().
        //  With this user table we want to access our users ID of the logged in user.

        $profileData = User::find($id);

        return view('admin.admin_profile', compact('profileData'));
    }
    public function ProfileStore(Request $request) { // This line defines a method called ProfileStore that takes a Request object as a parameter. This method is responsible for handling the form submission when the admin updates their profile information.
        $id = Auth::user()->id; // This line retrieves the ID of the currently authenticated user using the Auth facade. The ID is stored in the $id variable, which will be used to find the corresponding user record in the database.
        $data = User::find($id); // This line uses the User model to find the user record in the database based on the retrieved ID. The found user record is stored in the $data variable, which will be used to update the user's profile information.

        // Updating the user's profile information with the data received from the request. The name, email, phone, and address fields are updated with the corresponding values from the request object.
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhotoPath = $data->photo; // This line retrieves the current photo path of the user from the $data object and stores it in the $oldPhotoPath variable. This is done to keep track of the old photo path before updating it with a new one.

        if($request->hasFile('photo')) { // This line checks if a new photo file is uploaded in the request. If a new photo is uploaded, the code inside the if block will be executed to handle the photo upload and update the user's profile photo.

            $file = $request->file('photo'); // This line retrieves the uploaded photo file from the request and stores it in the $file variable. The file object will be used to perform operations related to the uploaded photo.

            $filename = time() . '.' . $file->getClientOriginalExtension(); // This line generates a new filename for the uploaded photo by appending the current timestamp (time()) to the original file extension of the uploaded photo. This helps ensure that each uploaded photo has a unique filename. For example, if the original file extension is .jpg, the new filename will be something like 1623456789.jpg.

            $file->move(public_path('upload/user_images'), $filename); // This line moves the uploaded photo file to the specified directory (public/upload/user_images) with the new filename generated in the previous line. The public_path() function is used to get the absolute path to the public directory of the Laravel application. This ensures that the uploaded photo is stored in a publicly accessible location.

            $data->photo = $filename; // This line updates the user's profile photo path in the $data object with the new filename of the uploaded photo. This ensures that when the user profile is saved, it will reference the new photo.

            if($oldPhotoPath && $oldPhotoPath !== $filename) { // This line checks if there is an old photo path stored in the $oldPhotoPath variable and if it is different from the new filename. If both conditions are true, it means that the user has uploaded a new photo, and the old photo needs to be deleted from the server to free up storage space and avoid clutter.

                $this->deleteOldImage($oldPhotoPath); // This line calls a method named deleteOldImage, passing the old photo path as an argument. The purpose of this method is to handle the deletion of the old photo file from the server. The implementation of this method is not shown in the provided code snippet, but it is assumed to be defined elsewhere in the AdminController class.
            }
        }

        $data->save();

        // Setting a notification message to be displayed after the profile update. The $notification array contains a message indicating that the admin profile was updated successfully and an alert type of 'success'. This notification can be used in the view to display a success message to the user after the profile update operation is completed.
        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); // This line redirects the user back to the previous page (usually the profile page) after the profile update operation is completed. The with() method is used to pass the $notification array to the redirected page, allowing it to be accessed and displayed in the view.
    }

    private function deleteOldImage(string $oldPhotoPath) : void { // This line defines a private method named deleteOldImage that takes a string parameter $oldPhotoPath. The purpose of this method is to handle the deletion of the old photo file from the server. The : void return type indicates that this method does not return any value.

        $fullPath = public_path('upload/user_images/' . $oldPhotoPath); // This line constructs the full path to the old photo file by concatenating the public directory path (obtained using public_path()) with the relative path to the user images directory and the old photo filename. The resulting full path is stored in the $fullPath variable.

        if(file_exists($fullPath)) { // This line checks if the file at the constructed full path exists on the server. If the file exists, it means that there is an old photo that needs to be deleted.

            unlink($fullPath);
        }
    }

    public function PasswordUpdate(Request $request) {

        $user = Auth::user(); // Get the currently authenticated user
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        // Check if the old password matches the current password
        if (!Hash::check($request->old_password, $user->password)) { // Hash::check() is a method provided by Laravel's Hash facade that compares a plain text password (the first argument) with a hashed password (the second argument). It returns true if the plain text password matches the hashed password, and false otherwise. In this case, it checks if the old password entered by the user matches the current password stored in the database for the authenticated user.

            $notification = array(
                'message' => 'Old password does not match',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        // Update the password
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password) // Hash::make() is a method provided by Laravel's Hash facade that hashes a plain text password using a secure hashing algorithm (bcrypt by default). It takes the plain text password as an argument and returns the hashed version of the password. In this case, it hashes the new password entered by the user before storing it in the database.
        ]);

        Auth::logout(); // Log out the user after updating the password

        $notification = array(
            'message' => 'Password updated successfully. Please log in again.',
            'alert-type' => 'success'
        );

        return redirect()->route('login')->with($notification);
    }

}
