<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Supporting Auth
use Illuminate\Support\Facades\Auth;
use App\Models\WareHouse;

class WareHouseController extends Controller
{
    //
    public function AllWareHouse()
    {
        // Accessing/getting all latest data from warehouses table
        $warehouse = WareHouse::latest()->get();
        return view('admin.backend.warehouse.all_warehouse', compact('warehouse'));
    }

    public function AddWareHouse()
    {
        return view('admin.backend.warehouse.add_warehouse');
    }

    public function StoreWareHouse(Request $request)
    {
        // Storing the data in the faq table
        WareHouse::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
        ]);


        // Displaying notification
        $notification = array(
            'message' => 'WareHouse Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.warehouse')->with($notification);
    }

    public function EditWareHouse($id) {
        $warehouse = WareHouse::findOrFail($id);
        return view('admin.backend.warehouse.edit_warehouse', compact('warehouse'));
    }

    public function UpdateWareHouse(Request $request) {

        $warehouse_id = $request->id; // Get the WareHouse ID from the request

            WareHouse::find($warehouse_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city,
            ]);

            // Displaying notification
            $notification = array(
                'message' => 'WareHouse Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.warehouse')->with($notification);

    }

    public function DeleteWareHouse($id) {

        $warehouse = WareHouse::findOrFail($id);

        // Deletes the image file from the server
        WareHouse::findOrFail($id)->delete(); // Deletes the review record from the database

        // Displaying notification
        $notification = array(
            'message' => 'WareHouse Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
