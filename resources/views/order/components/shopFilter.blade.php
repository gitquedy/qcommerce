<div class="btn-group mb-1">
    <input type="hidden" id="shop" name="shop" class="selectFilter">
    <input type="hidden" id="timings" name="timings" class="selectFilter">
    <input type="hidden" id="shipping_status" name="shipping_status" class="selectFilter">
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="fa fa-shopping-cart"></i> All Shops
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
          {{-- <a class="dropdown-item shop_filter_btn" href="#" data-shop_id="all">All Shop</a> --}}
        @foreach($all_shops as $shop)
          <a class="dropdown-item filter_btn" href="#" data-target="shop" data-type="multiple" data-value="{{ $shop->id }}">{!! $shop->getImgSiteDisplayWithFullName() !!}</a>
        @endforeach
      </div>
    </div>
  </div>