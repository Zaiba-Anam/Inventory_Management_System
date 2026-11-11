@extends('admin.admin_master')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-xxl">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">All Product Category</h4>
                </div>

                <div class="text-end">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#standard-modal">
                            Add Category
                        </button>
                    </div>
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
                                        <th>Category Name</th>
                                        <th>Category Slug</th>
                                        <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($category as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->category_name }}</td>
                                            <td>{{ $item->category_slug }}</td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#category" id="{{ $item->id }}"
                                                    onclick="categoryEdit(this.id)">
                                                    Edit
                                                </button>
                                                <a href="{{ route('delete.category', $item->id) }}"
                                                    class="btn btn-danger btn-sm" id="delete">Delete</a>
                                                {{-- <a href="{{ route('edit.category', $item->id) }}"
                                                    class="btn btn-success btn-sm">Edit</a>
                                                <a href="{{ route('delete.category', $item->id) }}"
                                                    class="btn btn-danger btn-sm" id="delete">Delete</a> --}}
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


    <!-- Default Modal -->
    <div class="modal fade" id="standard-modal" tabindex="-1" aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="standard-modalLabel">Product Category</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('store.category') }}" method="POST">
                        @csrf
                        <div class="form-group col-md-12">
                            <label for="input1" class="form-label">Product Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="input1">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Edit --}}
    <div class="modal fade" id="category" tabindex="-1" aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="standard-modalLabel">Edit Product Category</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update.category') }}" method="POST">
                        @csrf

                        <input type="hidden" name="cat_id" id="cat_id">
                        <div class="form-group col-md-12">
                            <label for="input1" class="form-label">Product Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="cat">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script>
        function categoryEdit(id){
            $.ajax({ //calling ajax with $
                type: 'GET', //type property
                url: '/edit/category/'+id, // type property with requested id
                dataType: 'json', //JSON format data

                success:function(data){ // if succes we want to receive it as data
                    console.log(data);

                    // Receiving the data
                    $('#cat').val(data.category_name);
                    $('#cat_id').val(data.id);
                }
            })
        }
    </script>
@endsection
