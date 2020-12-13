<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>
@php 

 $setting=DB::table('sitesetting')->first();
@endphp

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ env('APP_URL') }}/public/setting/121220_17_48_21.png" style="width:100%; max-width:300px;">
                            </td>
                            
                            <td>
                                Invoice #: {{ str_pad($allinfo['orderSingle']->id, 8, '0', STR_PAD_LEFT)      }}<br>
                                Created: {{ date('d-m-Y',strtotime($allinfo['orderSingle']->created_at)) }}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                               {{ $setting->company_name }}<br>
                                {{ $setting->company_address }}<br>
                                Sunnyville, CA 12345
                            </td>
                            
                            <td>
                                {{ $allinfo['customer_details']->name }}
                                <br>
                                {{ $allinfo['customer_details']->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                
                <td>
                    Amount
                </td>
            </tr>
            
            <tr class="details">
                <td>
                    @if($allinfo['payment_type'] == 'bkash')
                        {{ $allinfo['payment_type'] }}  <br>
                        Trx ID: {{ $allinfo['bkash_trx_no'] }}

                    @endif    

                    @if($allinfo['payment_type'] == 'cod') 
                        Cash On Delivery  
                    @endif

                </td>
                
                <td>
                    1000
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Item
                </td>

                <td>
                    Quantity
                </td>

                
                <td>
                    Price
                </td>
            </tr>
            @foreach($allinfo['orderDetails'] as $orderItem)
            <tr class="item">
                <td>
                    {{ $orderItem->product_name }}
                </td>

                   <td>
                    {{ $orderItem->quantity }} Pcs
                </td>
                
                <td>
                  {{ $orderItem->singleprice * $orderItem->quantity }} Tk
                </td>
            </tr>

            @endforeach
            
             <tr class="total">
                <td>Shipping Charge</td>
                <td>-</td>
                <td col>
                   50 Tk
                </td>
            </tr>
    
           <tr>
               <hr>
           </tr>
            <tr class="total">
                <td></td>
                
                <td>
                   Total:
                </td>
                <td>
                    {{ $allinfo['total'] }} Tk
                </td>
            </tr>
        </table>
    </div>
</body>
</html>