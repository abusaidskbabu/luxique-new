@extends('backend.layouts.master')
@section('title','Supplier Update - '.config('concave.cnf_appname'))
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Supplier > Update Supplier</span>
                <a class="btn btn-success float-right" href="{{ route('admin.supplier')}}">View Supplier List</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" method="post" action="{{ route('admin.supplier.update', $supplier->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Supplier Name <span style="color: #f00">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" class="form-control " value="{{$supplier->name}}" placeholder="Name" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row ">
                                    <div class="col-sm-3"><label class="col-form-label">Defalut Image</label></div>
                                      <div class="col-sm-9">
                                         <button type="button"
                                             data-image-width="800" 
                                             data-image-height="800"  
                                             data-input-name="image" 
                                             data-input-type="single" 
                                             class="btn btn-success initConcaveMedia" >Select File
                                          </button>
                                            @if($supplier->image)
                                            <p class="selected_images_gallery">
                                                <span>
                                                    <input type="hidden" value="{{$supplier->image}}" name="image">
                                                    <img src="{{'/'.$supplier->image}}"> 
                                                    <b data-file-url="{{$supplier->image}}" class="selected_image_remove">X</b>
                                                </span>
                                            </p>
                                            @endif
                                      </div>
                                 </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Company Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="company_name" value="{{$supplier->company_name}}" class="form-control " placeholder="Company Name" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Phone <span style="color: #f00">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="phone_number" value="{{$supplier->phone_number}}" class="form-control " placeholder="Phone" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Email </label>
                                    <div class="col-sm-9">
                                        <input type="email" name="email" value="{{$supplier->email}}" class="form-control " placeholder="Email" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Status </label>
                                    <div class="col-sm-9">
                                       <div class="form-check form-check-flat">
                                          <label class="switch"><input name="is_active" type="checkbox" @if($supplier->is_active == 1) checked @endif required><span class="slider round"></span></label>
                                       </div>
                                    </div>
                                 </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address </label>
                                    <div class="col-sm-10">
                                       <textarea name="address" class="form-control" id="" cols="30" rows="2">{{$supplier->address}}</textarea>
                                    </div>
                                 </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" type="submit">Update Supplier</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function () {
                $(".tag_field").tagsinput('items');
            })
        </script>
    @endpush

@endsection
