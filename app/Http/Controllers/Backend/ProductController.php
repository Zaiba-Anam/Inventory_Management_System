<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Supplier;
use App\Models\WareHouse;
use App\Models\Brand;


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    //
    public function AllCategory()
    {
        $category = ProductCategory::latest()->get();
        return view('admin.backend.category.all_category', compact('category'));
    }

    public function StoreCategory(Request $request)
    {
        ProductCategory::insert([
            'category_name' => $request->category_name,
            'category_slug' => Strtolower(str_replace(' ', '-', $request->category_name)),
        ]);
        // Displaying notification
        $notification = array(
            'message' => 'Category Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function EditCategory($id)
    {
        $category = ProductCategory::find($id);
        return response()->json($category);
    }

    public function UpdateCategory(Request $request)
    {
        $cat_id = $request->cat_id;
        ProductCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' => Strtolower(str_replace(' ', '-', $request->category_name)),
        ]);
        // Displaying notification
        $notification = array(
            'message' => 'Category Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function DeleteCategory($id)
    {

        $category = ProductCategory::findOrFail($id);

        // Deletes the image file from the server
        ProductCategory::findOrFail($id)->delete(); // Deletes the review record from the database

        // Displaying notification
        $notification = array(
            'message' => 'Category Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    // Add Product all methods
    // First add 'use App\Models\Product;' and 'use App\Models\ProductImage;' at the top
    public function AllProduct()
    {
        //  Getting the data from the products table.
        $allData = Product::orderBy('id', 'desc')->get();
        return view('admin.backend.product.product_list', compact('allData'));
    }

    // First support Supplier, Brand, WareHouse at the top because we add data from here
    public function AddProduct()
    {
        // Getting ProductCategory data
        $categories = ProductCategory::all();
        // Getting Brand data
        $brands = Brand::all();
        // Getting Supplier data
        $suppliers = Supplier::all();
        // Getting WareHouse data
        $warehouses = WareHouse::all();

        return view('admin.backend.product.add_product', compact('categories', 'brands', 'suppliers', 'warehouses'));
    }

    public function StoreProduct(Request $request)
    {
        // Storing the data in products table
        $product = Product::create([
            'name' => $request->name,
            'code' => $request->code,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'warehouse_id' => $request->warehouse_id,
            'supplier_id' => $request->supplier_id,
            'price' => $request->price,
            'stock_alert' => $request->stock_alert,
            'note' => $request->note,
            'product_qty' => $request->product_qty,
            'status' => $request->status,
            'created_at' => now(),
        ]);

        // Accessing the product_id
        $product_id = $product->id;

        /// Multiple Image Upload
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $manager = new ImageManager(new Driver());

                // Generating a unique name
                $name_gen = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension(); // eg: 25611561651.jpg

                // Resize image
                $imgs = $manager->read($img);
                $imgs->resize(150, 150)->save(public_path('upload/productimg/' . $name_gen));
                $save_url = 'upload/productimg/' . $name_gen;

                ProductImage::create([
                    'product_id' => $product_id,
                    'image' => $save_url
                ]);
                $notification = array(
                    'message' => 'Product Inserted successfully.',
                    'alert-type' => 'success'
                );

                return redirect()->route('all.product')->with($notification);
            }
        }
    }

    public function EditProduct($id)
    {
        $editData = Product::find($id);
        // Getting ProductCategory data
        $categories = ProductCategory::all();
        // Getting Brand data
        $brands = Brand::all();
        // Getting Supplier data
        $suppliers = Supplier::all();
        // Getting WareHouse data
        $warehouses = WareHouse::all();
        $multiimg = ProductImage::where('product_id', $id)->get();

        return view('admin.backend.product.edit_product', compact('categories', 'brands', 'suppliers', 'warehouses', 'editData', 'multiimg'));
    }

    public function UpdateProduct(Request $request)
    {
        // Product Id
        $pro_id = $request->id;

        $product = Product::findOrFail($pro_id);

        $product->name = $request->name;
        $product->code = $request->code;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->warehouse_id = $request->warehouse_id;
        $product->supplier_id = $request->supplier_id;
        $product->price = $request->price;
        $product->stock_alert = $request->stock_alert;
        $product->note = $request->note;
        $product->product_qty = $request->product_qty;
        $product->status = $request->status;
        $product->save();

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $manager = new ImageManager(new Driver());

                // Generating a unique name
                $name_gen = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension(); // eg: 25611561651.jpg

                // Resize image
                $imgs = $manager->read($img);
                $imgs->resize(150, 150)->save(public_path('upload/productimg/' . $name_gen));

                $product->images()->create([
                    'image' => 'upload/productimg/' . $name_gen
                ]);
            }
        }

        if ($request->has('remove_image')) {
            foreach ($request->remove_image as $removeImageId) {
                $img = ProductImage::find($removeImageId);
                if ($img) {
                    if (file_exists(public_path($img->image))) {
                        unlink(public_path($img->image));
                    }
                    $img->delete();
                }
            }
        }

        $notification = array(
            'message' => 'Product Updated successfully.',
            'alert-type' => 'success'
        );
        return redirect()->route('all.product')->with($notification);
    }

    public function DeleteProduct($id) {

        $product = Product::findOrFail($id);

        // Delete Associated Images
        $images = ProductImage::where('product_id', $id)->get();

        foreach($images as $img){
            $imagePath = public_path($img->image);
            if(file_exists($imagePath)){
                unlink($imagePath); // Deletes the image file from the server
            }
        }


        ProductImage::where('product_id', $id)->delete(); // Deletes the review record from the database

        $product->delete();

        // Displaying notification
        $notification = array(
            'message' => 'Product Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function DetailsProduct($id) {
        $product = Product::findOrFail($id);
        return view('admin.backend.product.details_product', compact('product'));
    }
}
