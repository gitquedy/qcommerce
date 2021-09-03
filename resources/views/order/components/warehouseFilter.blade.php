<div class="btn-group mb-1">
    <input type="hidden" id="warehouse" name="warehouse" class="selectFilter">
    <input type="hidden" id="timings" name="timings" class="selectFilter">
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-archive"></i> All Warehouses
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
            @foreach($all_warehouses as $warehouse)
            <a class="dropdown-item filter_btn" href="#" data-target="warehouse" data-type="multiple" data-value="{{ $warehouse->id }}">{!! $warehouse->name !!}</a>
            @endforeach
        </div>
    </div>
</div>