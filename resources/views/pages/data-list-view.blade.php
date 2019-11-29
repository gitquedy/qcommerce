
@extends('layouts/contentLayoutMaster')

@section('title', 'List View')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/file-uploaders/dropzone.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
{{-- Data list view starts --}}
<section id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Delete</a>
            <a class="dropdown-item" href="#">Print</a>
            <a class="dropdown-item" href="#">Archive</a>
            <a class="dropdown-item" href="#">Another Action</a>
          </div>
        </div>
      </div>
    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th></th>
            <th>NAME</th>
            <th>CATEGORY</th>
            <th>POPULARITY</th>
            <th>ORDER STATUS</th>
            <th>PRICE</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
            @if($product["order_status"] === 'delivered')
              <?php $color = "success" ?>
            @elseif($product["order_status"] === 'pending')
              <?php $color = "primary" ?>
            @elseif($product["order_status"] === 'on hold')
              <?php $color = "warning" ?>
            @elseif($product["order_status"] === 'canceled')
              <?php $color = "danger" ?>
            @endif
            <?php 
              $arr = array('success', 'primary', 'info', 'warning', 'danger');
            ?>
            
            <tr>
              <td></td>
              <td class="product-name">{{ $product["name"] }}</td>
              <td class="product-category">{{ $product["category"] }}</td>
              <td>
                <div class="progress progress-bar-{{ $arr[array_rand($arr)] }}">
                  <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                    style="width:{{ $product["popularity"] }}%"></div>
                </div>
              </td>
              <td>
                <div class="chip chip-{{$color}}">
                  <div class="chip-body">
                    <div class="chip-text">{{ $product["order_status"]}}</div>
                  </div>
                </div>
              </td>
              <td class="product-price">{{ $product["price"] }}</td>
            </tr>
          @endforeach
        </tbody>
        {{-- <tbody>
          <tr>
            <td></td>
            <td class="product-name">Apple Watch series 4 GPS</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-success">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:97%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$69.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Beats HeadPhones</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:83%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-success">
                <div class="chip-body">
                  <div class="chip-text">Delivered</div>
                </div>
              </div>
            </td>
            <td class="product-price">$69.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Altec Lansing - Bluetooth Speaker</td>
            <td class="product-category">Audio</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:57%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-danger">
                <div class="chip-body">
                  <div class="chip-text">canceled</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Aluratek - Bluetooth Audio Receiver</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:65%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$29.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Aluratek - Bluetooth Audio Transmitter</td>
            <td class="product-category">Audio</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-danger">
                <div class="chip-body">
                  <div class="chip-text">canceled</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Basis - Peak Fitness and Sleep Tracker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:47%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Antec - Nano Diamond Thermal Compound</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:55%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-primary">
                <div class="chip-body">
                  <div class="chip-text">pending</div>
                </div>
              </div>
            </td>
            <td class="product-price">$29.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Antec - SmartBean Bluetooth Adapter</td>
            <td class="product-category">Computer</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:63%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-danger">
                <div class="chip-body">
                  <div class="chip-text">canceled</div>
                </div>
              </div>
            </td>
            <td class="product-price">$39.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Beats by Dr. Dre - 3' USB-to-Micro USB Cable</td>
            <td class="product-category">Computer</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-success">
                <div class="chip-body">
                  <div class="chip-text">delivered</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Beats by Dr. Dre - Bike Mount for Pill Speakers</td>
            <td class="product-category">Audio</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:40%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">delivered</div>
                </div>
              </div>
            </td>
            <td class="product-price">$49.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Bose® - SoundLink® Color Bluetooth Speaker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:90%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-primary">
                <div class="chip-body">
                  <div class="chip-text">pending</div>
                </div>
              </div>
            </td>
            <td class="product-price">$129.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">BRAVEN - Portable Bluetooth Speaker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Craig - Portable Wireless Speaker</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-danger">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:20%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-danger">
                <div class="chip-body">
                  <div class="chip-text">canceled</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Definitive Technology - Wireless Speaker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:75%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-primary">
                <div class="chip-body">
                  <div class="chip-text">pending</div>
                </div>
              </div>
            </td>
            <td class="product-price">$399.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Fitbit - Charge HR Activity Tracker + Heart Rate (Large)</td>
            <td class="product-category">Audio</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:60%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-primary">
                <div class="chip-body">
                  <div class="chip-text">pending</div>
                </div>
              </div>
            </td>
            <td class="product-price">$149.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Fitbit - Flex 1" USB Charging Cable</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$14.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Fitbit - Activity Tracker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-danger">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:35%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-danger">
                <div class="chip-body">
                  <div class="chip-text">canceled</div>
                </div>
              </div>
            </td>
            <td class="product-price">$99.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Fitbit - Charge Wireless Activity Tracker (Large)</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-primary">
                <div class="chip-body">
                  <div class="chip-text">pending</div>
                </div>
              </div>
            </td>
            <td class="product-price">$129.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Craig - Tower Speaker</td>
            <td class="product-category">Audio</td>
            <td>
              <div class="progress progress-bar-warning">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:68%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$69.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">BRAVEN - Outdoor Speaker</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:97%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-success">
                <div class="chip-body">
                  <div class="chip-text">delivered</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Bose® - Bluetooth Speaker Travel Bag</td>
            <td class="product-category">Computers</td>
            <td>
              <div class="progress progress-bar-primary">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:89%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-warning">
                <div class="chip-body">
                  <div class="chip-text">on hold</div>
                </div>
              </div>
            </td>
            <td class="product-price">$44.99</td>
          </tr>
          <tr>
            <td></td>
            <td class="product-name">Altec Lansing - Mini H2O Bluetooth Speaker</td>
            <td class="product-category">Fitness</td>
            <td>
              <div class="progress progress-bar-success">
                <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100"
                  style="width:87%"></div>
              </div>
            </td>
            <td>
              <div class="chip chip-success">
                <div class="chip-body">
                  <div class="chip-text">delivered</div>
                </div>
              </div>
            </td>
            <td class="product-price">$199.99</td>
          </tr>
        </tbody> --}}
      </table>
    </div>
    {{-- DataTable ends --}}

    {{-- add new sidebar starts --}}
    <div class="add-new-data-sidebar">
      <div class="overlay-bg"></div>
      <div class="add-new-data">
        <form action="data-list-view" method="POST">
          @csrf
          <div class="div mt-2 px-2 d-flex new-data-title justify-content-between">
            <div>
              <h4>ADD NEW DATA</h4>
            </div>
            <div class="hide-data-sidebar">
              <i class="feather icon-x"></i>
            </div>
          </div>
          <div class="data-items pb-3">
            <div class="data-fields px-2 mt-3">
              <div class="row">
                <div class="col-sm-12 data-field-col">
                  <label for="data-name">Name</label>
                  <input type="text" class="form-control" name="name" id="data-name">
                </div>
                <div class="col-sm-12 data-field-col">
                  <label for="data-category"> Category </label>
                  <select class="form-control" name="category" id="data-category">
                    <option>Audio</option>
                    <option>Computers</option>
                    <option>Fitness</option>
                    <option>Appliance</option>
                  </select>
                </div>
                <div class="col-sm-12 data-field-col">
                  <label for="data-status">Order Status</label>
                  <select class="form-control" id="data-status" name="order_status">
                    <option>Pending</option>
                    <option>Cancelled</option>
                    <option>Delivered</option>
                    <option>On Hold</option>
                  </select>
                </div>
                <div class="col-sm-12 data-field-col">
                  <label for="data-price">Price</label>
                  <input type="number" class="form-control" name="price" id="data-price">
                </div>
                <div class="col-sm-12 data-field-col">
                  <label for="data-popularity">Popularity</label>
                  <input type="number" class="form-control" name="popularity" id="data-popularity">
                </div>
                <div class="col-sm-12 data-field-col data-list-upload">
                  <div class="dropzone dropzone-area" action="#" id="dataListUpload" name="img">
                    <div class="dz-message">Upload Image</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
            <div class="add-data-btn">
              <input type="submit" class="btn btn-primary" value="Add Data">
            </div>
            <div class="cancel-data-btn">
              <input type="reset" class="btn btn-outline-danger" value="Cancel">
            </div>
          </div>
        </form>
      </div>
    </div>
    {{-- add new sidebar ends --}}
  </section>
  {{-- Data list view end --}}
@endsection
@section('vendor-script')
{{-- vednor js files --}}
        <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
@endsection
@section('myscript')
        {{-- Page js files --}}
        <script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
