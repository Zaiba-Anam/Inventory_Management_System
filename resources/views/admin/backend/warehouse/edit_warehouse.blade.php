@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="content">

        <!-- Start Content-->
        <div class="container-xxl">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Add WareHouse</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-pane pt-4" id="profile_setting" role="tabpanel" aria-labelledby="setting_tab">
                    <div class="row">

                        <!-- Personal Information -->
                        <div class="col-lg-12 col-xl-12">
                            <div class="card border mb-0">
                                <form action="{{ route('update.warehouse') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <input type="hidden" name="id" value="{{ $warehouse->id }}">
                                    <div class="card-body">

                                        <div class="form-group mb-3">
                                            <label class="form-label">WareHouse Name</label>
                                            <input class="form-control" type="text" name="name"  value="{{ $warehouse->name }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Email</label>
                                            <input class="form-control" type="email" name="email"  value="{{ $warehouse->email }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone</label>
                                            <input class="form-control" type="text" name="phone"  value="{{ $warehouse->phone }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">City</label>
                                            <input class="form-control" type="text" name="city"  value="{{ $warehouse->city }}">
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


</script>
@endsection
