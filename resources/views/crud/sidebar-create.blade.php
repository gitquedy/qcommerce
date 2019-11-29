<div class="add-new-data-sidebar">
  <div class="overlay-bg"></div>
    <div class="add-new-data">
      <form action="{{ action('CrudController@store') }}" method="POST" class="form" enctype="multipart/form-data">
        @csrf
        <div class="div mt-2 px-2 d-flex new-data-title justify-content-between">
          <div>
            <h4>ADD NEW CRUD</h4>
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
            </div>
          </div>
        </div>
        <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
          <div class="add-data-btn">
            <input type="submit" name="saveandadd" class="btn btn-primary btn_save" value="Add Data">
          </div>
          <div class="cancel-data-btn">
            <button type="reset" class="btn btn-outline-danger">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>