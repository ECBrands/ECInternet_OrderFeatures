# Magento2 Module ECInternet OrderFeatures
``ecinternet/order_features - 1.4.5.0``

- [Requirements](#requirements-header)
- [Overview](#overview-header)
- [Installation](#installation-header)
- [Configuration](#configuration-header)
- [Design Modifications](#design-modifications-header)
- [Specifications](#specifications-header)
- [Attributes](#attributes-header)
- [Notes](#notes-header)
- [Version History](#version-history-header)

## Requirements

## Overview
OrderFeatures contains multiple pieces of functionality which extend existing logic in the Magento 2 "Sales" module, specifically the Order sub-module.

## Installation
- Unzip the zip file in `app/code/ECInternet`
- Enable the module by running `php bin/magento module:enable ECInternet_OrderFeatures`
- Apply database updates by running `php bin/magento setup:upgrade`
- Flush the cache by running `php bin/magento cache:flush`

## Configuration
- Set $0 order totals when Customer uses 'ecinternet_free' Payment method.

## Design Modifications
### Overridden Layouts
- `adminhtml`
  - `sales_order_view`
- `frontend`
  - `checkout_index_index`
  - `customer_account_index`
  - `sales_order_shipment`
  - `sales_order_view`
  - `shipping_tracking_popup`


### Overridden Templates
- `adminhtml`
  - `order/info.phtml`
  - `order/recent.phtml`

## Specifications
- Payment Method
  - ERP Terms (`ecinternet_erpterms`)
  - Admin-Only Free Payment Method (`ecinternet_free`)
- Shipping Method
  -  Admin-Only Shipping Method (`ecinternet_admin_only`)
- Order Status
  - Fully Shipped (`fully_shipped`) Assigned to state `complete`
  - Partially Shipped (`partially_shipped`) Assigned to state `processing`
  - Staging (`staging`) - Assigned to state `processing`

## Attributes
- Customer
  - ERP Terms (`erp_terms`)
  - ShipVia Code (`ship_via_code`)
  - ShipVia Desc (`ship_via_desc`)
- CustomerAddress
  - ShipVia Code (`ship_via_code`)
  - ShipVia Desc (`ship_via_desc`)
- Invoice
  - ERP Terms (`erp_terms`)
  - Order Comment (`order_comment`)
  - PO Number (`po_number`)
  - Ship Via (`ship_via`)
- Order
  - ERP Terms (`erp_terms`)
  - External Order Reference (`external_order_reference`)
  - Order Comment (`order_comment`)
  - Placed In Admin (`placed_in_admin`)
  - PO Number (`po_number`)
  - ShipVia (`ship_via`)
- Quote
  - ERP Terms (`erp_terms`)
  - Order Comment (`order_comment`)
  - Placed In Admin (`placed_in_admin`)
  - PO Number (`po_number`)
  - Ship Via (`ship_via`)

## Notes
### Writing back `external_order_reference` to Magento
Issue a `POST` request to `/rest/V1/orders` with the following payload:
```json
{
    "entity": {
        "entity_id": 1,
        "extension_attributes": {
            "external_order_reference": "MyExternalOrderReference"
        }
    }
}
```

## Known Issues

## Version History
- 1.4.5.0
  - Add ability to trigger customer notifications for instore pickup.
- 1.4.4.0
  - Added "Skippable Skus" list for blacklisting product SKUs when determining if shipment has fully shipped all products.
- 1.4.3.0
  - Assigned custom order status `partially_shipped` to state `processing`.
  - Assigned custom order status `fully_shipped` to state `complete`.
- 1.4.2.0
  - Assigned custom order status `staging` to state `processing`.
- 1.4.1.0
  - Fixed bug with "Enable payment billing addresses" setting.
- 1.3.8.2
  - Set `external_order_reference` Order grid columns to be non-sortable.
- 1.3.8.1
  - Set `order_placement_location` and `shipment_tracking_numbers` Order grid columns to be non-sortable.
- 1.3.8.0
  - Settings added to show/hide `po_number` and `order_comment` inputs in Checkout.
  - Updated `po_number` and `order_comment` inputs to be collapsible.
  - Added `external_order_reference` to Order ExtensionAttributes.
  - Added admin order creation support for Erpterms Payment Method.
  - Added `fully_shipped` order status.
  - Fully shipped shipments will now be given `fully_shipped` status automatically.  Previous value was `complete`.
  - Added `external_order_reference` and `shipment_tracking_numbers` to admin Order grid.
- 1.3.6.0
  - Updated UpgradeData to not show Customer attributes in admin grid.  This could lead to compilation errors for some databases.
- 1.3.5.0
  - Fixed `ecinternet_free` payment method to not be available for frontend checkout.
- 1.1.1.0
  - Added `erp_terms` to Order ExtensionAttributes.
- 1.1.0.1
  - Fixed `UpgradeSchema.php` to not error out if creating duplicate order statuses.
  - Added back necessary Payment/Free model.
- 1.1.0.0
  - Fixed `ecinternet_free` payment method override to correctly add new method to admin screen.
- 1.0.5.0
  - Fixed bug where order grand total was being set to $0 when the free payment method was NOT used.
