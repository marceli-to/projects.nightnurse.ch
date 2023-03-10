<style>

@font-face {
  font-family: 'MetaLight';
  src: url('{{ url("/") }}/assets/css/fonts/MetaLight.ttf') format("truetype");
  font-weight: 300;
  font-style: normal; 
}

@font-face {
  font-family: 'MetaRegular';
  src: url('{{ url("/") }}/assets/css/fonts/MetaRegular.ttf') format("truetype");
  font-weight: 400;
  font-style: normal; 
}

@font-face {
  font-family: 'MetaBold';
  src: url('{{ url("/") }}/assets/css/fonts/MetaBold.ttf') format("truetype");
  font-weight: 700;
  font-style: normal; 
}

@page {
  size: A4;
  margin: 5mm 12mm 10mm 30mm;
}

@media print {
  html, body {
    margin: 0;
    width: 210mm;
    height: 297mm;
  }
}

body {
  color: #000000;
  font-size: 9pt;
  font-family: 'MetaRegular', Helvetica, Arial, sans-serif;
  font-weight: 400;
  line-height: .9;
  text-rendering: optimizeLegibility;
}

strong {
  font-family: 'MetaBold', Helvetica, Arial, sans-serif;
  font-weight: 700;
}

table {
  border-collapse: collapse;
  border-spacing: 0;
}

td {
  vertical-align: top;
}

th {
  font-family: 'MetaRegular', Helvetica, Arial, sans-serif;
  font-weight: 400;
  text-align: left;
}

img {
  border: 0;
  vertical-align: middle;
}

table {
  width: 100%;
}

table td {
  text-align: left;
  vertical-align: top;
}

h1 {
  font-family: 'MetaLight', Helvetica, Arial, sans-serif;
  font-weight: 300;
  line-height: .9;
  font-size: 14.5pt;
}

h2, h3 {
  font-family: 'MetaBold', Helvetica, Arial, sans-serif;
  font-weight: 700;
}

p {
  margin-bottom: 5mm;
}

ul, li {
  margin: 0;
  padding: 0;
}

li {
  margin-left: 4mm;
}

.quote-body {
  padding-top: 50mm;
  width: 140mm;
}

.quote-header {
  top: 0;
  left: 0;
  position: fixed;
}

.quote-header__brand {
  position: fixed;
  top: 27mm;
  right: -3mm;
}

.quote-header__brand img {
  display: inline-block;
  height: 12mm;
  width: 10.99mm;
}

.quote-items {
  border-top: 0.3pt solid #333;
  page-break-after: auto;
  width: 100%;
}

.quote-items tr {
  border-bottom: 0.3pt solid #333 !important;
}

.quote-items tr:last-child {
  border-bottom: none !important;
}

.quote-items.is-total {
  margin-top: 7mm;
  margin-bottom: 10mm;
}

.quote-items.is-total tr:nth-child(1),
.quote-items.is-total tr:nth-child(3) {
  border-top: 0.3pt solid #333;
}

.quote-items td {
  padding: .6mm 0 .8mm 0;
}

.quote-items.is-total tr:last-of-type {
  border-bottom: none;
}

.quote-items__description {
  width: 66%;
}

.quote-items__line-price {
  width: calc(17% - 25px);
}

.quote-items__line-total {
  text-align: right;
  width: calc(17% - 25px);
}

/* Helpers */
.align-right {
  text-align: right;
}

.align-left {
  text-align: left;
}

.clearfix:after {
  visibility: hidden;
  display: block;
  font-size: 0;
  content: " ";
  clear: both;
  height: 0;
}

.footer {
  bottom: 0;
  font-family: 'MetaBold', Helvetica, Arial, sans-serif;
  line-height: .8;
  font-weight: 700;
  position: fixed;
}

.break {
  page-break-after: always;
}

</style>