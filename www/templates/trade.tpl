<script type="text/template" id="trade_template">
  <tr id="trade-<%= id %>">
    <td><%= user_id %></td>
    <td><%= currency_from %></td>
    <td><%= currency_to %></td>
    <td><%= amount_buy %></td>
    <td><%= amount_sell %></td>
    <td><%= rate %></td>
    <td><%= originating_country %></td>
    <td><%= time_placed %></td>
  </tr>
</script>