const staffExcerpt = document.querySelector('#staff-excerpt');

const showExcerpt = (e) => {
	e.preventDefault();
	staffExcerpt.innerHTML = '';
	staffExcerpt.classList.remove('show');
	let excerpt = '';
	switch (e.target.textContent) {
		case 'Administrative Staff':
			excerpt = staffexcerpt.administrative;
			break;
		case 'Clinical Team':
			excerpt = staffexcerpt.clinical;
			break;
		case 'Medical Team':
			excerpt = staffexcerpt.medical;
			break;
		case 'Recovery Coach':
			excerpt = staffexcerpt.recovery;
			break;
		case 'Support Staff':
			excerpt = staffexcerpt.support;
			break;
		case 'Wellness':
			excerpt = staffexcerpt.wellness;
			break;
		default:
			excerpt = '';
	}

	if (excerpt.length > 0) {
		staffExcerpt.innerHTML = excerpt;
		staffExcerpt.classList.add('show');
		const closeButton = document.createElement('button');
		closeButton.classList.add('close-staff-excerpt');
		closeButton.textContent = 'X';
		closeButton.addEventListener('click', closeExcerpt, { once: true });
		staffExcerpt.append(closeButton);
	}
}

const closeExcerpt = () => {
	staffExcerpt.classList.remove('show');
}

const orcStaffMemberDepartments = document.querySelectorAll('[data-vc-grid-filter] li span');
[...orcStaffMemberDepartments].forEach((department) => {
	department.addEventListener('click', showExcerpt, false);
});
