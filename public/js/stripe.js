// document.addEventListener('DOMContentLoaded', function() {
  
//   const stripeKey = "{{ env('STRIPE_KEY') }}";
//   const stripe = Stripe(stripeKey);

//   const elements = stripe.elements();
//   const cardElement = elements.create('card');
//   cardElement.mount('#card-element');

//   const cardHolderName = document.getElementById('card-holder-name');
//   const cardButton = document.getElementById('card-button');
//   const clientSecret = cardButton.dataset.secret;

//   // エラーメッセージを表示するdiv要素を取得する
//   const cardError = document.getElementById('card-error');
//   // エラーメッセージを表示するul要素を取得する
//   const errorList = document.getElementById('error-list');

//   // Card Elementのオプションを設定して、郵便番号を非表示にする
//   const card = elements.create('card', {
//     hidePostalCode: true
//   });
//   // var card = elements.create('card', {style: style, hidePostalCode: true});

//   cardButton.addEventListener('click', async (e) => {
//     const { setupIntent, error } = await stripe.confirmCardSetup(
//       clientSecret, {
//       payment_method: {
//         card: cardElement,
//         billing_details: { name: cardHolderName.value }
//       }
//     }
//     );

//     if (cardHolderName.value === '' || error) {
//       while (errorList.firstChild) {
//         errorList.removeChild(errorList.firstChild);
//       }

//       if (cardHolderName.value === '') {
//         cardError.style.display = 'block';

//         let li = document.createElement('li');
//         li.textContent = 'カード名義人の入力は必須です。';
//         errorList.appendChild(li);
//       }

//       if (error) {
//         console.log(error);
//         cardError.style.display = 'block';
//         let li = document.createElement('li');
//         li.textContent = error['message'];
//         errorList.appendChild(li);
//       }
//     } else {
//       stripePaymentIdHandler(setupIntent.payment_method);
//     }
//   });

//   function stripePaymentIdHandler(paymentMethodId) {
//     const form = document.getElementById('card-form');

//     const hiddenInput = document.createElement('input');
//     hiddenInput.setAttribute('type', 'hidden');
//     hiddenInput.setAttribute('name', 'paymentMethodId');
//     hiddenInput.setAttribute('value', paymentMethodId);
//     form.appendChild(hiddenInput);

//     form.submit();
//   }
// });








// document.addEventListener('DOMContentLoaded', function() {
  
//   const stripeKey = "{{ env('STRIPE_KEY') }}";
//   const stripe = Stripe(stripeKey);

//   const elements = stripe.elements();
  
//   // Card Elementのオプションを設定して、郵便番号を非表示にする
//   const cardElement = elements.create('card', {
//     hidePostalCode: true
//   });

//   cardElement.mount('#card-element');

//   const cardHolderName = document.getElementById('card-holder-name');
//   const cardButton = document.getElementById('card-button');
//   const clientSecret = cardButton.dataset.secret;

//   // エラーメッセージを表示するdiv要素を取得する
//   const cardError = document.getElementById('card-error');
//   // エラーメッセージを表示するul要素を取得する
//   const errorList = document.getElementById('error-list');

//   cardButton.addEventListener('click', async (e) => {
//     const { setupIntent, error } = await stripe.confirmCardSetup(
//       clientSecret, {
//       payment_method: {
//         card: cardElement,  // cardをcardElementに変更
//         billing_details: { name: cardHolderName.value }
//       }
//     }
//     );


//     cardButton.addEventListener('click', async (e) => {
//       e.preventDefault();
      
//       const postalCode = document.getElementById('postal-code').value;
      
//       if (!/^\d{3}-\d{4}$/.test(postalCode)) {
//           // エラーメッセージを表示するための処理を追加可能
//           alert('郵便番号は、例：123-4567形式で入力してください。');
//           return;
//       }

//       const { setupIntent, error } = await stripe.confirmCardSetup(
//           clientSecret, {
//           payment_method: {
//               card: cardElement,
//               billing_details: { 
//                   name: cardHolderName.value,
//                   address: {
//                       postal_code: postalCode
//                   }
//               }
//           }
//       });

//       // ... 残りのコード ...
//   });


//     if (cardHolderName.value === '' || error) {
//       while (errorList.firstChild) {
//         errorList.removeChild(errorList.firstChild);
//       }

//       if (cardHolderName.value === '') {
//         cardError.style.display = 'block';

//         let li = document.createElement('li');
//         li.textContent = 'カード名義人の入力は必須です。';
//         errorList.appendChild(li);
//       }

//       if (error) {
//         console.log(error);
//         cardError.style.display = 'block';
//         let li = document.createElement('li');
//         li.textContent = error['message'];
//         errorList.appendChild(li);
//       }
//     } else {
//       stripePaymentIdHandler(setupIntent.payment_method);
//     }
//   });

//   function stripePaymentIdHandler(paymentMethodId) {
//     const form = document.getElementById('card-form');

//     const hiddenInput = document.createElement('input');
//     hiddenInput.setAttribute('type', 'hidden');
//     hiddenInput.setAttribute('name', 'paymentMethodId');
//     hiddenInput.setAttribute('value', paymentMethodId);
//     form.appendChild(hiddenInput);

//     form.submit();
//   }
// });









// stripe.js
document.addEventListener('DOMContentLoaded', function() {
  const stripeKey = "{{ env('STRIPE_KEY') }}";
  const stripe = Stripe(stripeKey);
  const elements = stripe.elements();

  const cardElement = elements.create('card', {
      style: {
          base: {
              // 基本スタイル
              fontSize: '16px',
              color: '#32325d',
          },
          invalid: {
              color: '#fa755a',
          }
      },
      hidePostalCode: true // 郵便番号フィールドを非表示にする
  });

  cardElement.mount('#card-element');

  const cardHolderName = document.getElementById('card-holder-name');
  const cardButton = document.getElementById('card-button');
  const clientSecret = cardButton.dataset.secret;

  const cardError = document.getElementById('card-error');
  const errorList = document.getElementById('error-list');

  cardButton.addEventListener('click', async (e) => {
      e.preventDefault();
      
      const postalCode = document.getElementById('postal-code').value;
      if (!/^\d{3}-\d{4}$/.test(postalCode)) {
          alert('郵便番号は、例：123-4567形式で入力してください。');
          return;
      }

      const { setupIntent, error } = await stripe.confirmCardSetup(
          clientSecret, {
              payment_method: {
                  card: cardElement,
                  billing_details: {
                      name: cardHolderName.value,
                      address: {
                          postal_code: postalCode
                      }
                  }
              }
          });

      if (cardHolderName.value === '' || error) {
          while (errorList.firstChild) {
              errorList.removeChild(errorList.firstChild);
          }
          if (cardHolderName.value === '') {
              cardError.style.display = 'block';
              let li = document.createElement('li');
              li.textContent = 'カード名義人の入力は必須です。';
              errorList.appendChild(li);
          }
          if (error) {
              console.log(error);
              cardError.style.display = 'block';
              let li = document.createElement('li');
              li.textContent = error.message;
              errorList.appendChild(li);
          }
      } else {
          stripePaymentIdHandler(setupIntent.payment_method);
      }
  });

  function stripePaymentIdHandler(paymentMethodId) {
      const form = document.getElementById('card-form');

      const hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'paymentMethodId');
      hiddenInput.setAttribute('value', paymentMethodId);
      form.appendChild(hiddenInput);

      form.submit();
  }
});

