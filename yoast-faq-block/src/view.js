console.log('testing faq block')


// add FAQ active class on loaded
window.onload = function() {
	let $elem = document.querySelector('.wp-block-create-block-ekwa-yoast-faq .schema-faq-section:first-child');
    let $elem2 = document.querySelectorAll('.wp-block-create-block-ekwa-yoast-faq .schema-faq-section:first-child');
	console.log($elem2);
    // $elem.classList.add('active');
    // $elem2.forEach();
    $elem2.forEach(function (el, index) {
        el.classList.add('active');
    });
}

// add FAQ active class on click
let $clickFaq = document.getElementsByClassName('schema-faq-question');

for (let i = 0; i < $clickFaq.length; i++) {
	$clickFaq[i].addEventListener('click', function(e) {
		var $items = document.querySelectorAll('.schema-faq-section');
		[].forEach.call($items, function(el) {
			el.classList.remove('active');
		});

		let $elem = this.closest('.schema-faq-section');
		let $elemId = $elem.getAttribute('id');
		let $item = document.getElementById($elemId);
		$item.classList.add('active');
	});
}