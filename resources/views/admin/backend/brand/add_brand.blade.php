@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="content">

        <!-- Start Content-->
        <div class="container-xxl">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Add Brand</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-pane pt-4" id="profile_setting" role="tabpanel" aria-labelledby="setting_tab">
                    <div class="row">

                        <!-- Personal Information -->
                        <div class="col-lg-12 col-xl-12">
                            <div class="card border mb-0">
                                <form action="{{ route('store.brand') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="card-body">

                                        <div class="form-group mb-3">
                                            <label class="form-label">Brand Name</label>
                                            <input class="form-control" type="text" name="name">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Image</label>
                                            <input class="form-control" type="file" name="image" id="image">
                                        </div>

                                        <div class="form-group mb-3">
                                            <img id="showImage" class="rounded-circle avatar-lg img-thumbnail"
                                                src="{{ url('upload/no_image.jpg') }}" alt="image profile">
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

<script type="text/javascript">
    // This line indicates that the following code is written in JavaScript. The type attribute specifies the scripting language used in the script element. In this case, it is JavaScript.
    $(document).ready(
        function() { // This line means that the code inside this function will run when the document is fully loaded and ready. It ensures that the DOM is ready before executing any jQuery code.
            $('#image').change(function(
                e
                ) { // This line sets up an event listener for the change event on the element with the ID image. When the user selects a new file (image) in the file input, this function will be triggered.
                //  e is the event object that contains information about the event that occurred. In this case, it represents the change event triggered by selecting a new file in the file input.
                var reader =
                    new FileReader(); // This line creates a new instance of the FileReader object. The FileReader is used to read the contents of files (in this case, the selected image file) asynchronously.
                reader.onload = function(
                    e
                ) { // This line defines a callback function that will be executed when the FileReader has finished reading the file. The e parameter contains information about the event, including the result of the file reading operation.
                    // The onload event is triggered when the file has been successfully read, and the result can be accessed through e.target.result.
                    // The reader object is used to read the contents of the selected file, and when the reading is complete, the onload event is fired, allowing us to access the result and perform actions with it.

                    $('#showImage').attr('src', e.target
                        .result
                    ); // This line selects the element with the ID showImage (the image element where the preview will be displayed) and sets its src attribute to the result of the file reading operation. The e.target.result contains the data URL of the selected image, which allows it to be displayed in the img tag.
                    // The attr() method is used to set the attribute value of the selected element. In this case, it updates the src attribute of the image element to show the preview of the selected image.
                    // The e.target.result contains the data URL of the selected image, which can be used to display the image in an img tag or perform other operations with the image data.
                }
                reader.readAsDataURL(e.target.files[
                    '0'
                ]); // This line initiates the reading of the selected file as a data URL. The e.target.files['0'] refers to the first file selected by the user in the file input. The readAsDataURL method reads the file and triggers the onload event when it's done, allowing the image preview to be displayed.
            });
        });
</script>
@endsection
