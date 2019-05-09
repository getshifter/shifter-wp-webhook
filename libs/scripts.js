function getWPAPIUrl( w = window ) {
  const defaultUrl = location.origin + '/wp-json/'
  const setting = w.wpApiSettings
  if ( ! setting || !setting.root ) return defaultUrl
  return setting.root
}
function createRequestObject( w = window) {
  const url = getWPAPIUrl( w )
  const request = {
    url: url + 'shifter/v1/webhook',
    method: 'POST',
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', w.wpApiSettings.nonce );
    },
  }
  return request
}

function showUnregistWebhookNotification() {
  swal( {
    title: 'Webhook not registered',
    text: [
      'You have not been registered a webhook URL.',
      'Please visit Shifter webhook setting page and put a webhook URL'
    ].join('\n'),
    padding: '3em',
    showCancelButton: true,
    confirmButtonColor: 'transparent',
    cancelButtonColor: '#333',
    confirmButtonText: 'Go to Setting'
  } )
  .then( function( result ) {
    if ( result.dismiss ) return
    if ( ! result.value ) return
    location.href = location.origin + '/wp-admin/admin.php?page=shifter-webhook';
  })
}

function postWebhook() {
  const conf = window.Shifter_Webhook
  if (!conf || !conf.hasWebhook) return showUnregistWebhookNotification()
  swal( {
    title: 'Are you sure?',
    text: "Confirm to execute the webhook.",
    padding: '3em',
    showCancelButton: true,
    confirmButtonColor: 'transparent',
    cancelButtonColor: '#333',
    confirmButtonText: 'Execute',
  } )
  .then( function( result ) {
    if ( result.dismiss ) return
    if ( ! result.value ) return
    return jQuery.ajax( createRequestObject() )
      .then( function() {
        swal(
          'Webhook executed',
          'Please check the result in your subscribed service',
          'success'
        )
      } )
  } ).catch( function( err ) {
    console.log(err)
    console.log(JSON.stringify(err.responseJSON, null, 2))
    swal(
      'Failed to execute',
      [
        err.responseJSON ? `<pre style="text-align: left; overflow: scroll;">${JSON.stringify(err.responseJSON, null, 2)}</pre>`|| 'Internal Error' : 'Internal Error'
      ].join('<br/>'),
      'error'
    )
  } )
}

jQuery( document )
  .on( "click" , "#wp-admin-bar-send-webhook" , function() {
    postWebhook()
  });


