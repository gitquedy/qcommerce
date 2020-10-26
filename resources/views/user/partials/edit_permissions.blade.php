<div class="row">
  <div class="col-12">
  <div class="table-responsive border rounded px-1">
    <h6 class="border-bottom py-1 mx-1 mb-0 font-medium-2"><i
        class="feather icon-lock mr-50 "></i>Permission</h6>
    <table class="table table-borderless">
      <thead>
        <tr>
          <th>Main</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="order.manage" name="permissions[]"  {{ $user->can('order.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-shopping-bag"></i></span>
              </span>
              <span class="">Orders</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="product.manage" name="permissions[]"  {{ $user->can('product.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-package"></i></span>
              </span>
              <span class="">Products</span>
            </div></fieldset>
          </td>
          <td></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th>Shop Access</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($shops as $items)
        <tr>
          <td></td>
          @foreach($items as $shop)
            <td>
              <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" value="{{ $shop->id }}" name="shops[]" {{ $user->shopPermissions->contains('shop_id', $shop->id) ? 'checked' : '' }}>
                <span class="vs-checkbox">
                  <span class="vs-checkbox--check"><i class="vs-icon feather icon-shopping-cart"></i></span>
                </span>
                <span class="">{!! $shop->getImgSiteDisplay()!!}</span>
              </div></fieldset>
            </td>
            @endforeach
        </tr>
        @endforeach
      </tbody>
       <thead>
        <tr>
          <th>Reconciliation</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="returnRecon.manage" name="permissions[]" {{ $user->can('returnRecon.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-x-square"></i></span>
              </span>
              <span class="">Returns</span>
            </div></fieldset>
          </td>
          
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="payoutRecon.manage" name="permissions[]" {{ $user->can('payoutRecon.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-dollar-sign"></i></span>
              </span>
              <span class="">Payout</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="shippingFeeRecon.manage" name="permissions[]" {{ $user->can('shippingFeeRecon.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-truck"></i></span>
              </span>
              <span class="">Shipping Fee's</span>
            </div></fieldset>
          </td>
        </tr>
      </tbody>
       <thead>
        <tr>
          <th>Inventory</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="sku.manage" name="permissions[]" {{ $user->can('sku.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-box"></i></span>
              </span>
              <span class="">Sku's</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="brand.manage" name="permissions[]" {{ $user->can('brand.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-grid"></i></span>
              </span>
              <span class="">Brands</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="category.manage" name="permissions[]" {{ $user->can('category.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-tag"></i></span>
              </span>
              <span class="">Categories</span>
            </div></fieldset>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="supplier.manage" name="permissions[]" {{ $user->can('supplier.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-users"></i></span>
              </span>
              <span class="">Suppliers</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="barcode.manage" name="permissions[]" {{ $user->can('barcode.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-hash"></i></span>
              </span>
              <span class="">Barcode</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="adjustment.manage" name="permissions[]" {{ $user->can('adjustment.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-repeat"></i></span>
              </span>
              <span class="">Adjustment</span>
            </div></fieldset>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="transfer.manage" name="permissions[]" {{ $user->can('transfer.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-log-in"></i></span>
              </span>
              <span class="">Transfers</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="purchase.manage" name="permissions[]" {{ $user->can('purchase.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-shopping-cart"></i></span>
              </span>
              <span class="">Purchases</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="expense.manage" name="permissions[]" {{ $user->can('expense.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-dollar-sign"></i></span>
              </span>
              <span class="">Expenses</span>
            </div></fieldset>
          </td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th>Warehouse Access</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($warehouses as $items)
        <tr>
          <td></td>
          @foreach($items as $warehouse)
            <td>
              <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" value="{{ $warehouse->id }}" name="warehouses[]" {{ $user->warehousePermissions->contains('warehouse_id', $warehouse->id) ? 'checked' : '' }}>
                <span class="vs-checkbox">
                  <span class="vs-checkbox--check"><i class="vs-icon feather icon-server"></i></span>
                </span>
                <span class="">{!! $warehouse->name!!}</span>
              </div></fieldset>
            </td>
            @endforeach
        </tr>
        @endforeach
      </tbody>
      <thead>
        <tr>
          <th>Users</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="user.manage" name="permissions[]" {{ $user->can('user.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-users"></i></span>
              </span>
              <span class="">Users</span>
            </div></fieldset>
          </td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th>Offline Sales</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="sales.manage" name="permissions[]" {{ $user->can('sales.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-shopping-cart"></i></span>
              </span>
              <span class="">Sales</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="customer.manage" name="permissions[]" {{ $user->can('customer.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-user-check"></i></span>
              </span>
              <span class="">Customer</span>
            </div></fieldset>
          </td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="deposit.manage" name="permissions[]" {{ $user->can('deposit.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-dollar-sign"></i></span>
              </span>
              <span class="">Deposit</span>
            </div></fieldset>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="payment.manage" name="permissions[]" {{ $user->can('payment.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-dollar-sign"></i></span>
              </span>
              <span class="">Payment</span>
            </div></fieldset>
          </td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th>Reports</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="report.manage" name="permissions[]" {{ $user->can('report.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-file"></i></span>
              </span>
              <span class="">Reports</span>
            </div></fieldset>
          </td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th>Settings</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>
            <fieldset><div class="vs-checkbox-con vs-checkbox-primary">
              <input type="checkbox" value="settings.manage" name="permissions[]" {{ $user->can('settings.manage') ? 'checked' : '' }}>
              <span class="vs-checkbox">
                <span class="vs-checkbox--check"><i class="vs-icon feather icon-settings"></i></span>
              </span>
              <span class="">Settings</span>
            </div></fieldset>
          </td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>   
  </div>
</div>
</div>