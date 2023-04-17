<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paypal</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?=$client_id?>"></script>
    <h1>Pagos con PayPal</h1>
    <div id="paypalCard">

    </div>
    <script>

  paypal.Buttons({
    createOrder:function(data,actions) {
       return actions.order.create({
            purchase_units:[{
                description:"Super Product",
                amount:{
                    value:"0.1"
                }
            }],
            application_context:{
            application__preference:"NO_SHIPPING"
            }
       });
    },
    onApprove:function(data, actions){
        alert("Tu orden: "+data.orderID);
        fetch("process/"+data.orderID ,{ //busca la respuesta de esa url
          method:"POST",
        })
        .then(res=>res.json()) //convierte la respuesta json a un objeto js para poder acceder a sus propiedades es el equivalente de un json decode
        .then(res=>{
          console.log(res);
          alert(res.msj); //obtenemos la propiedad mensaje
        })
      }
  }).render('#paypalCard');

</script>
</body>
</html>