@extends('layouts/contentLayoutMaster')

@section('title', 'Users Statistics')

@section('vendor-style')
        <!-- Vendor css files -->
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/select.dataTables.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
@endsection
@section('mystyle')
        <!-- Page css files -->
        <link rel="stylesheet" href="{{ asset(mix('css/pages/app-users.css')) }}">
@endsection
@section('content')
<!-- User Table -->
<section>
  <!-- Begin: User form -->
  <div class="card">
    <div class="card-body">
      <form class="form">
        <div class="form-body">
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12">
              <label for="customSelect1">Role</label>
              <fieldset class="form-group">
                <select class="form-control" id="customSelect1">
                  <option selected>Author</option>
                  <option value="Customer">Customer</option>
                  <option value="Staff">Staff</option>
                  <option value="Suppliers">Suppliers</option>
                  <option value="Partner">Partner</option>
                </select>
              </fieldset>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
              <label for="customSelect2">Status</label>
              <fieldset class="form-group">
                <select class="form-control" id="customSelect2">
                  <option selected>Active</option>
                  <option value="Suspended">Suspended</option>
                  <option value="Pending">Pending</option>
                </select>
              </fieldset>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
              <label for="customSelect3">Latest Activity</label>
              <fieldset class="form-group">
                <select class="form-control" id="customSelect3">
                  <option selected>Any</option>
                  <option value="weekAgo">Week ago</option>
                  <option value="monthAgo">Month ago</option>
                  <option value="yearAgo">Year ago</option>
                </select>
              </fieldset>
            </div>
            <div
              class="col-lg-3 col-md-6 col-sm-12 d-flex justify-content-around"
            >
              <div class="vs-checkbox-con vs-checkbox-primary mr-2">
                <input type="checkbox" value="false" />
                <span class="vs-checkbox">
                  <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                  </span>
                </span>
                <span class="">Verified</span>
              </div>
              <div class="result-btn">
                <button
                  type="button"
                  class="btn mt-2 btn-outline-primary text-uppercase"
                >
                  Show Results
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- End: User form -->

  <!-- Begin Users Profile -->
  <div class="card">
    <div class="card-header">
      <div class="user-actions">
        <div class="dropdown d-inline-block">
          <button
            class="btn btn-outline-primary dropdown-toggle mr-1 round"
            type="button"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            Actions
          </button>
          <div
            class="dropdown-menu dropdown-menu-right"
            aria-labelledby="dropdownMenuButton"
          >
            <a class="dropdown-item" href="#">Archive</a>
            <a class="dropdown-item" href="#">Delete</a>
            <a class="dropdown-item" href="#">Print</a>
          </div>
        </div>
        <button
          type="button"
          class="btn btn-primary d-inline-block "
          data-toggle="modal"
          data-target="#inlineForm"
        >
          Add Users
        </button>
        <div
          class="modal fade text-left"
          id="inlineForm"
          tabindex="-1"
          role="dialog"
          aria-labelledby="myModalLabel33"
          aria-hidden="true"
        >
          <div
            class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
            role="document"
          >
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Add User</h4>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="#">
                <div class="modal-body">
                  <label>Name </label>
                  <div class="form-group">
                    <input
                      type="text"
                      placeholder="Name"
                      class="form-control"
                    />
                  </div>
                  <label>Email ID</label>
                  <div class="form-group">
                    <input
                      type="text"
                      placeholder="Email address"
                      class="form-control"
                    />
                  </div>
                  <label>User Name</label>
                  <div class="form-group">
                    <input
                      type="text"
                      placeholder="User Name"
                      class="form-control"
                    />
                  </div>
                  <label for="customSelect6">Role</label>
                  <fieldset class="form-group">
                    <select class="form-control" id="customSelect6">
                      <option selected>Admin</option>
                      <option value="Customer">Customer</option>
                      <option value="Staff">Staff</option>
                      <option value="Suppliers">Suppliers</option>
                      <option value="Partner">Partner</option>
                    </select>
                  </fieldset>
                  <label for="customSelect7">Status</label>
                  <fieldset class="form-group">
                    <select class="form-control" id="customSelect7">
                      <option selected>Active</option>
                      <option value="Suspended">Suspended</option>
                      <option value="Pending">Pending</option>
                    </select>
                  </fieldset>
                </div>
                <div class="modal-footer">
                  <button
                    type="button"
                    class="btn btn-primary"
                    data-dismiss="modal"
                  >
                    Add
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger"
                    data-dismiss="modal"
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table zero-configuration" id="check-slct">
          <thead>
            <tr class="text-uppercase">
              <th></th>
              <th>Name</th>
              <th>Email ID</th>
              <th>User Name</th>
              <th>Latest Activity</th>
              <th>Role</th>
              <th>Status</th>
              <th>Verified</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-20.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Lara</span>
                </div>
              </td>
              <td>LaraElliott@jourrapide.com</td>
              <td>Aforessind</td>
              <td>05/23/2019</td>
              <td>
                <div class="chip chip-success">
                  <div class="chip-body">
                    <div class="chip-text">Customer</div>
                  </div>
                </div>
              </td>
              <td class="text-warning">Pending</td>
              <td>
                <i
                  class="feather icon-x text-danger font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-1.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>André</span>
                </div>
              </td>
              <td>AndreBarros@jourrapide.com</td>
              <td>Eiver1954</td>
              <td>08/23/2019</td>
              <td>
                <div class="chip chip-warning">
                  <div class="chip-body">
                    <div class="chip-text">Staff</div>
                  </div>
                </div>
              </td>
              <td class="text-danger">Suspended</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-2.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Emily</span>
                </div>
              </td>
              <td>EmilyHarrington@jourrapide.com</td>
              <td>Deplivars</td>
              <td>18/23/2019</td>
              <td>
                <div class="chip chip-primary">
                  <div class="chip-body">
                    <div class="chip-text">Author</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-3.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Garland</span>
                </div>
              </td>
              <td>GarlandFrechette@rhyta.com</td>
              <td>Carablathe</td>
              <td>20/05/2019</td>
              <td>
                <div class="chip chip-danger">
                  <div class="chip-body">
                    <div class="chip-text">Partner</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-4.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Qiong</span>
                </div>
              </td>
              <td>QiongChung@armyspy.com</td>
              <td>Beemeart</td>
              <td>25/05/2019</td>
              <td>
                <div class="chip chip-info">
                  <div class="chip-body">
                    <div class="chip-text">Supplier</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-5.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Dimitri</span>
                </div>
              </td>
              <td>DimitriEsposito@teleworm.us</td>
              <td>Beemeart</td>
              <td>1/06/2019</td>
              <td>
                <div class="chip chip-success">
                  <div class="chip-body">
                    <div class="chip-text">Customer</div>
                  </div>
                </div>
              </td>
              <td class="text-danger">Suspended</td>
              <td>
                <i
                  class="feather icon-x text-danger font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-6.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Yesenia</span>
                </div>
              </td>
              <td>YeseniaNevzorova@armyspy.com</td>
              <td>Grainky</td>
              <td>9/06/2019</td>
              <td>
                <div class="chip chip-success">
                  <div class="chip-body">
                    <div class="chip-text">Customer</div>
                  </div>
                </div>
              </td>
              <td class="text-warning">Pending</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-7.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Enzo</span>
                </div>
              </td>
              <td>EnzoBjork@teleworm.us</td>
              <td>Sical1966</td>
              <td>14/06/2019</td>
              <td>
                <div class="chip chip-primary">
                  <div class="chip-body">
                    <div class="chip-text">Author</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-8.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Emilie</span>
                </div>
              </td>
              <td>EmilieKlaskova@dayrep.com</td>
              <td>Nount1998</td>
              <td>15/06/2019</td>
              <td>
                <div class="chip chip-success">
                  <div class="chip-body">
                    <div class="chip-text">Customer</div>
                  </div>
                </div>
              </td>
              <td class="text-danger">Suspended</td>
              <td>
                <i
                  class="feather icon-x text-danger font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-9.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Philipp</span>
                </div>
              </td>
              <td>PhilippFruehauf@jourrapide.com</td>
              <td>Saight</td>
              <td>23/06/2019</td>
              <td>
                <div class="chip chip-warning">
                  <div class="chip-body">
                    <div class="chip-text">Staff</div>
                  </div>
                </div>
              </td>
              <td class="text-danger">Suspended</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-10.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Brianna</span>
                </div>
              </td>
              <td>BriannaKelly@teleworm.us</td>
              <td>Cappereen</td>
              <td>1/07/2019</td>
              <td>
                <div class="chip chip-success">
                  <div class="chip-body">
                    <div class="chip-text">Customer</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-11.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Milo</span>
                </div>
              </td>
              <td>MiloGoodchild@dayrep.com</td>
              <td>Onsille</td>
              <td>5/07/2019</td>
              <td>
                <div class="chip chip-danger">
                  <div class="chip-body">
                    <div class="chip-text">Partner</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-12.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Johanna</span>
                </div>
              </td>
              <td>JohannaHursti@armyspy.com</td>
              <td>Memmar</td>
              <td>19/07/2019</td>
              <td>
                <div class="chip chip-primary">
                  <div class="chip-body">
                    <div class="chip-text">Author</div>
                  </div>
                </div>
              </td>
              <td class="text-warning">Pending</td>
              <td>
                <i
                  class="feather icon-x text-danger font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <div class="user-info">
                  <img
                    class="round mr-1"
                    src="../../../app-assets/images/portrait/small/avatar-s-13.png"
                    alt="avtar img holder"
                    height="32"
                    width="32"
                  />
                  <span>Edward</span>
                </div>
              </td>
              <td>EdwardFrazer@jourrapide.com</td>
              <td>Bable1995</td>
              <td>25/07/2019</td>
              <td>
                <div class="chip chip-info">
                  <div class="chip-body">
                    <div class="chip-text">Supplier</div>
                  </div>
                </div>
              </td>
              <td class="text-success">Active</td>
              <td>
                <i
                  class="feather icon-check text-success font-medium-3 ml-2 font-weight-bold"
                ></i>
              </td>
              <td>
                <i class="m-1 feather icon-edit-2 text-primary"></i>
                <i class="feather icon-trash text-primary"></i>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- End Users Profile -->
</section>
@endsection

@section('vendor-script')
  <!-- Vendor js files -->
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.select.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
@endsection
@section('myscript')
        <!-- Page js files -->
        <script src="{{ asset(mix('js/scripts/datatables/user-datatable.js')) }}"></script>
@endsection

