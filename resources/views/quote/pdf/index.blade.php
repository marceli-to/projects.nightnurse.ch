@include('quote.pdf.partials.header')
<div class="page">
  <section class="quote">
    <header class="quote-header">
      <h1>
        {{ __('Offerte') }} {{$data['quote']['QuoteNumber']}}<br>
        {{ $data['quote']['Name'] }}<br>
        Zürich, {!! AppHelper::formatedDate($data['quote']['LastModifiedDate']) !!}
      </h1>
      <div class="quote-header__brand">
        <img src="{{ asset('assets/img/logo-nightnurse.svg') }}" height="100" width="100">
      </div>
      <div class="quote-header__address">
        @if ($data['quote']['ShippingName']) {{ $data['quote']['ShippingName'] }}<br>@endif
        @if ($data['quote']['ShippingStreet']) {{ $data['quote']['ShippingStreet'] }}<br>@endif
        @if ($data['quote']['ShippingPostalCode']) {{ $data['quote']['ShippingPostalCode'] }} @endif
        @if ($data['quote']['ShippingCity']) {{ $data['quote']['ShippingCity'] }} @endif
      </div>
    </header>
    <div class="quote-body">
      @if (isset($data['quote']['Contact']['Email_Salutation__c']))
        <p>{{ $data['quote']['Contact']['Email_Salutation__c'] }}</p>
      @else
        <p>{{ __('Lieber') }} @if ($data['quote']['ShippingName']) {{ $data['quote']['ShippingName'] }}@endif</p>
      @endif
      <p>{{ __('Es freut uns, folgendes Angebot machen zu dürfen:') }}</p>
      @if (isset($data['quoteItems']) && count($data['quoteItems']) >= 1)
        @php $items = collect($data['quoteItems'])->sortBy('SortOrder'); @endphp
        <table class="quote-items">
          @foreach($items as $item)
            <tr>
              <td class="quote-items__description">{{$item['Product2']['Name']}}, {{$item['Product2']['Description']}}</td>
              <td class="quote-items__quantity">{{$item['Quantity']}} <em>x</em></td>
              <td class="quote-items__line-price">{!! QuoteHelper::moneyFormat($item['UnitPrice']) !!}</td>
              <td class="quote-items__line-total">{!! QuoteHelper::moneyFormat($item['Quantity'] * $item['UnitPrice']) !!}</td>
            </tr>
          @endforeach
        </table>
        <table class="quote-items is-total">
          <tr>
            <td class="quote-items__description">
              <strong>{{ __('Total exkl. MwSt.') }}</strong>
            </td>
            <td class="quote-items__quantity"></td>
            <td class="quote-items__line-price"></td>
            <td class="quote-items__line-total">
              <strong>{!! QuoteHelper::moneyFormat($data['quote']['Subtotal']) !!}</strong>
            </td>
          </tr>
          <tr>
            <td class="quote-items__description">{{ __('MwSt.') }}</td>
            <td class="quote-items__quantity">7.7%</td>
            <td class="quote-items__line-price">{!! QuoteHelper::moneyFormat($data['quote']['Subtotal']) !!}</td>
            <td class="quote-items__line-total">{!! QuoteHelper::moneyFormat($data['quote']['VAT_7_7__c']) !!}</td>
          </tr>
          <tr>
            <td class="quote-items__description">
              <strong>{{ __('Total inkl. MwSt. (alle Beträge in CHF)') }}</strong>
            </td>
            <td class="quote-items__quantity"></td>
            <td class="quote-items__line-price"></td>
            <td class="quote-items__line-total">
              <strong>{!! QuoteHelper::moneyFormat($data['quote']['Total_Invest__c']) !!}</strong>
            </td>
          </tr>
        </table>
      @endif
      <p>{{ __('Die nachfolgenden Seiten und unsere allgemeinen Geschäftsbedingungen sind Bestandteil dieser Offerte. Es ist uns ein wichtiges Anliegen, bei Veröffentlichungen als Urheber der Visualisierungen genannt zu werden, dazu weisen wir gerne auf den Abschnitt über die Immaterialgüterrechte hin.') }}</p>
      
      <div class="quote-details">
        @if (isset($data['quote']['Quote_Details__c']['Text__c']))
          {!! $data['quote']['Quote_Details__c']['Text__c'] !!}
        @endif
      </div>
  
    </div>
  </section>
</div>
@include('quote.pdf.partials.footer')