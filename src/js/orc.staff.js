const showExcerpt = (e) => {
	const staffExcerpt = document.querySelector('#staff-excerpt');
	switch (e.target.textContent) {
		case 'Administrative Staff':
			staffExcerpt.innerHTML = staffexcerpt.administrative;
			break;
		case 'Clinical Team':
			staffExcerpt.innerHTML = staffexcerpt.clinical;
			break;
		case 'Medical Team':
			staffExcerpt.innerHTML = staffexcerpt.medical;
			break;
		case 'Recovery Coach':
			staffExcerpt.innerHTML = staffexcerpt.recovery;
			break;
		case 'Support Staff':
			staffExcerpt.innerHTML = staffexcerpt.support;
			break;
		case 'Wellness':
			staffExcerpt.innerHTML = staffexcerpt.wellness;
			break;
		default:
			staffExcerpt.innerHTML = '';
	}
}

const orcStaffMemberDepartments = document.querySelectorAll('[data-vc-grid-filter] li span');

[...orcStaffMemberDepartments].forEach((department) => {
	department.addEventListener('click', showExcerpt, false);
});
