const doEmail = document.querySelectorAll('.doemail');
[...doEmail].forEach((email) => {
	email.addEventListener('click', (e) => {
		const whoToContact = e.target.dataset.whotocontact;
		const contactUs = contactdata.contactuspage;
		const caller = location.href;
		const mainContent = document.querySelector('#main-content');

		const form = document.createElement('form');
		form.style.display = 'none';
		form.id = 'gotocontacdtus';
		form.method = 'post';
		form.action = contactUs;

		let input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'whotocontact';
		input.value = whoToContact;
		form.append(input);

		input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'caller';
		input.value = caller;
		form.append(input);

		input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'contactus';
		input.value = contactUs;
		form.append(input);

		mainContent.append(form);
		form.submit();
	});
});
