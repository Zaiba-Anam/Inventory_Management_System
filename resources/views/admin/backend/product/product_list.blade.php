@extends('admin.admin_master')
@section('admin')

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">All Products</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <a href="{{ route('add.product') }}" class="btn btn-secondary">Add Product</a>
                </ol>
            </div>
        </div>

        <!-- Datatables  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>WareHouse</th>
                                <th>Price</th>
                                <th>In Stock</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach ($allData as $key=>$item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @php
                                                // $primaryImage = $item->image->first()->image ?? '/upload/no_image.jpg';
                                                $primaryImage = $item->images->first()->image ?? '/upload/no_image.jpg';
                                            @endphp
                                            <img src="{{ asset($primaryImage) }}" alt="img" width="40px">
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        {{-- <td>{{ $item->warehouse_id }}</td> --}}{{-- This get the id no. --}}
                                        <td>{{ $item['warehouse']['name'] }}</td>{{-- This gives the warehouse name for the particular id --}}
                                        <td>{{ $item->price }}</td>
                                        <td>
                                            @if ($item->product_qty <= 3)
                                                <span class="badge text-bg-danger">{{ $item->product_qty }}</span>
                                            @else
                                                <h4><span class="badge text-bg-secondary">{{ $item->product_qty }}</span></h4>
                                            @endif
                                        </td>
                                        <td>
                                            <a title="Details" href="{{ route('details.product', $item->id) }}" class="btn btn-info btn-sm"><span class="mdi mdi-eye mdi-18"></span></a>
                                            <a title="Edit" href="{{ route('edit.product', $item->id) }}" class="btn btn-success btn-sm"><span class="mdi mdi-book-edit mdi-18"></span></a>
                                            <a title="Delete" href="{{ route('delete.product', $item->id) }}" class="btn btn-danger btn-sm" id="delete"><span class="mdi mdi-delete mdi-18"></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div> <!-- container-fluid -->

</div> <!-- content -->

@endsection
