<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\Customer;

class SupplierController extends Controller
{
    //
    public function AllSupplier()
    {
        // Accessing/getting all latest data from suppliers table
        $supplier = Supplier::latest()->get();
        return view('admin.backend.supplier.all_supplier', compact('supplier'));
    }

    public function AddSupplier()
    {
        return view('admin.backend.supplier.add_supplier');
    }

    public function StoreSupplier(Request $request)
    {
        // Storing the data in the faq table
        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);


        // Displaying notification
        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }

    public function EditSupplier($id) {
        $supplier = Supplier::findOrFail($id);
        return view('admin.backend.supplier.edit_supplier', compact('supplier'));
    }

    public function UpdateSupplier(Request $request) {

        $supplier_id = $request->id; // Get the Supplier ID from the request

            Supplier::find($supplier_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Displaying notification
            $notification = array(
                'message' => 'Supplier Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.supplier')->with($notification);

    }

    public function DeleteSupplier($id) {

        $supplier = Supplier::findOrFail($id);

        // Deletes the image file from the server
        Supplier::findOrFail($id)->delete(); // Deletes the review record from the database

        // Displaying notification
        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    // Customers
    public function AllCustomer()
    {
        // Accessing/getting all latest data from customers table
        $customer = Customer::latest()->get();
        return view('admin.backend.customer.all_customer', compact('customer'));
    }

    public function AddCustomer()
    {
        return view('admin.backend.customer.add_customer');
    }

    public function StoreCustomer(Request $request)
    {
        // Storing the data in the faq table
        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);


        // Displaying notification
        $notification = array(
            'message' => 'Customer Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }

    public function EditCustomer($id) {
        $customer = Customer::findOrFail($id);
        return view('admin.backend.customer.edit_customer', compact('customer'));
    }

    public function UpdateCustomer(Request $request) {

        $customer_id = $request->id; // Get the Supplier ID from the request

            Customer::find($customer_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Displaying notification
            $notification = array(
                'message' => 'Customer Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.customer')->with($notification);

    }

    public function DeleteCustomer($id) {

        $customer = Customer::findOrFail($id);

        // Deletes the image file from the server
        Customer::findOrFail($id)->delete(); // Deletes the review record from the database

        // Displaying notification
        $notification = array(
            'message' => 'Customer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
