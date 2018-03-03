var cart_hash_key = substitutions.cart_hash_key;

function refresh_fragments() {
    jQuery( document.body ).trigger( 'wc_fragment_refresh' );
  console.log("refresh");
}
jQuery( document ).ready(function() {
  setTimeout(refresh_fragments, 400);
  setInterval(refresh_fragments, 60000);
});