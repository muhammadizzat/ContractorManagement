@extends('layouts.app')
@section('breadcrumbs', Breadcrumbs::render(Route::current()->getName()))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Form Layout</div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Email</label>
                                <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Password</label>
                                <input type="password" class="form-control" id="inputPassword4" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">

                                <label class="custom-toggle">
                                    <input type="checkbox">
                                    <span class="custom-toggle-slider rounded-circle"></span>
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="datePicker2">Date (Bootstrap-datepicker)</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control" id="datePicker2" placeholder="Choose Date">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Select (single)</label>
                                <select class="form-control" data-live-search="true">
                                    <option>Mustard</option>
                                    <option>Ketchup</option>
                                    <option>Barbecue</option>
                                </select>

                            </div>
                            <div class="form-group col-md-6">
                                <label>Select (multiple)</label>
                                <select class="form-control" multiple data-live-search="true">
                                    <option>Mustard</option>
                                    <option>Ketchup</option>
                                    <option>Barbecue</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Address</label>
                            <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                        </div>
                        <div class="form-group">
                            <label for="inputAddress2">Address 2</label>
                            <input type="text" class="form-control" id="inputAddress2"
                                placeholder="Apartment, studio, or floor">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputCity">City</label>
                                <input type="text" class="form-control" id="inputCity">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputState">State</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="inputZip">Zip</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">
                                        <span class="d-inline-block text-truncate w-75">Choose file</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileMultiple" multiple>
                                    <label class="custom-file-label" for="customFileMultiple">
                                        <span class="d-inline-block text-truncate w-75">Choose several files</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck">
                                <label class="form-check-label" for="gridCheck">
                                    Check me out
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Sign in</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                            Launch modal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmailModal">Email</label>
                            <input type="email" class="form-control" id="inputEmailModal" placeholder="Email">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPasswordModal">Password</label>
                            <input type="password" class="form-control" id="inputPasswordModal" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddressModal">Address</label>
                        <input type="text" class="form-control" id="inputAddressModal" placeholder="1234 Main St">
                    </div>
                    <div class="form-group">
                        <label for="inputAddress2Modal">Address 2</label>
                        <input type="text" class="form-control" id="inputAddress2Modal"
                            placeholder="Apartment, studio, or floor">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCityModal">City</label>
                            <input type="text" class="form-control" id="inputCityModal">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputStateModal">State</label>
                            <select id="inputStateModal" class="form-control">
                                <option selected>Choose...</option>
                                <option>...</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputZipModal">Zip</label>
                            <input type="text" class="form-control" id="inputZipModal">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFileModal">
                                <label class="custom-file-label" for="customFileModal">
                                    <span class="d-inline-block text-truncate w-75">Choose file</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFileMultipleModal" multiple>
                                <label class="custom-file-label" for="customFileMultipleModal">
                                    <span class="d-inline-block text-truncate w-75">Choose several files</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheckModal">
                            <label class="form-check-label" for="gridCheckModal">
                                Check me out
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function(){
        $('.input-group.date').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight : true,
            todayBtn : true,
            autoclose: true,
        })

        $('select').selectpicker();
    });
</script>
@endpush
