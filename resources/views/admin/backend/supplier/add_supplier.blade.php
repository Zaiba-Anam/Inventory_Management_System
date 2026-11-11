@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Add Supplier</h4>
            </div>
        </div>

        <div class="card-body">
            <div class="tab-pane pt-4" id="profile_setting" role="tabpanel" aria-labelledby="setting_tab">
                <div class="row">

                    <!-- Personal Information -->
                    <div class="col-lg-12 col-xl-12">
                        <div class="card border mb-0">
                            <form action="{{ route('store.supplier') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="card-body">

                                    <div class="form-group mb-3">
                                        <label class="form-label">Supplier Name</label>
                                        <input class="form-control" type="text" name="name" id="name">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email" id="email">
                                    </div>


                                    <div class="form-group mb-3">
                                        <label class="form-label">Phone</label>
                                        <input class="form-control" type="text" name="phone" id="phone">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" type="text" name="address" id="address"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Save Changes
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div> <!-- end education -->
        </div>
    </div>
</div>

@endsection
