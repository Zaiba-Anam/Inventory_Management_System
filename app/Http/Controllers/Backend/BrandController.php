<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use App\Models\Brand;

class BrandController extends Controller
{
    //
    public function AllBrand()
    {
        // Accessing/getting all latest data from brands table
        $brand = Brand::latest()->get();
        return view('admin.backend.brand.all_brand', compact('brand'));
    }

    public function AddBrand()
    {
        return view('admin.backend.brand.add_brand');
    }

    public function StoreBrand(Request $request)
    {
        // dd($request->all(), $request->file('image'));
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());

            // Generating a unique name
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // eg: 25611561651.jpg

            // Resize image
            $img = $manager->read($image);
            $img->resize(100, 90)->save(public_path('upload/brand/' . $name_gen));
            $save_url = 'upload/brand/' . $name_gen;

            Brand::create([
                'name' => $request->name,
                'image' => $save_url
            ]);
        }

        $notification = array(
            'message' => 'Brand Inserted successfully.',
            'alert-type' => 'success'
        );

        return redirect()->route('all.brand')->with($notification);
    }

    public function EditBrand($id) {
        $brand = Brand::findOrFail($id);
        return view('admin.backend.brand.edit_brand', compact('brand'));
    }

    public function UpdateBrand(Request $request) {

        $brand_id = $request->id; // Get the review ID from the request
        $brand = Brand::find($brand_id);

        if($request->file('image')) { // Checks whether an image was uploaded through the form with the field name image.
            $image = $request->file('image'); // Gets the uploaded image file.
            $manager = new ImageManager(new Driver()); // Creates an Intervention Image Manager using the GD driver.

            // **Here, we are generating a unique filename for the uploaded image using the hexdec(uniqid()) function, which generates a unique hexadecimal string based on the current time in microseconds. We then append the original file extension of the uploaded image to this unique string to create a new filename. This ensures that each uploaded image has a unique name, preventing any potential conflicts with existing files in the upload directory.
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // Generates a unique filename, for example: 184739201837.jpg

            // **Here, we are using the Intervention Image library to read the uploaded image, resize it to 60x60 pixels, and save it in the public/upload/brand/ directory with a unique name. The path to the saved image is then stored in the database along with the other review details. If no new image is uploaded, only the text fields are updated in the database.
            $img = $manager->read($image); // Reads the uploaded image using Intervention Image.

            // **The resize method is used to change the dimensions of the image to 60 pixels in width and 60 pixels in height. The save method is then called to save the resized image to the specified path, which is constructed using the public_path function to ensure it is saved in the public directory of the Laravel application. The unique filename generated earlier is appended to the path to avoid overwriting any existing files.
            $img->resize(60, 60)->save(public_path('upload/brand/'.$name_gen)); //Resizes the image to exactly 60 × 60 pixels and saves it inside: public/upload/brand/

            // **Here, we are constructing the URL to the saved image by concatenating the upload directory path with the unique filename generated earlier. This URL will be stored in the database so that it can be used to display the image in the application.
            $save_url = 'upload/brand/'.$name_gen;

            // Storing the data in the brands table

            Brand::find($brand_id)->update([
                'name' => $request->name,
                'image' => $save_url,
            ]);

            if (file_exists(public_path($brand->image))) { // Checks if the old image file exists in the public directory.
                unlink(public_path($brand->image)); // Deletes the old image file from the public directory to free up storage space and avoid clutter.
            }

            // Displaying notification
            $notification = array(
                'message' => 'Brand with Image Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.brand')->with($notification);
        } else {

            Brand::find($brand_id)->update([
                'name' => $request->name,
            ]);

            // Displaying notification
            $notification = array(
                'message' => 'Brand Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.brand')->with($notification);
        }
    }

    public function DeleteBrand($id) {

        $brand = Brand::findOrFail($id);

        $img = $brand->image;

        unlink($img); // Deletes the image file from the server
        Brand::findOrFail($id)->delete(); // Deletes the review record from the database

        // Displaying notification
        $notification = array(
            'message' => 'Brand Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}


