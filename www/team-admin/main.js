const api = "http://backend.localhost/api/v1"
var user = null
var team = null


const setToken = token => localStorage.setItem("auth", token)
const getToken = () => localStorage.getItem("auth")
const removeToken = () => localStorage.removeItem("auth")

const apiRequest = async (endpoint, method, form, callback, error) => {
	await fetch(`${api}${endpoint}`, {
		method: method,
		body: form == null ? null : JSON.stringify(form),
		headers: {
			'Authorization': `Bearer ${getToken()}`,
			'Content-Type': 'application/json'
		}
	})
	.then(response => response.json())
	.then(data => {
		if (callback) {
			callback(data)
		}
	})
	.catch(err => {
		if (error) {
			error(err)
		}
	})
}


/**
 * Login
 */
const loginModal = document.getElementById('loginModal')

const getTeamDetails = async () => {
	await apiRequest(`/teams/${user.teamId}`, 'GET', null, data => {
		team = data

		document.querySelector("input[name='teamName']").value = data.name
		document.querySelector("input[name='contactEmail']").value = data.contactEmail
		document.querySelector("input[name='location']").value = data.location
	}, err => console.error(err))
}

const login = async (form, success, failure) => {
	await fetch(`${api}/login`, {
		method: 'POST',
		body: form
	})
	.then(response => response.json())
	.then(data => {
		if (data.status == "success") {
			user = data.user
			setToken(user.token)

			getTeamDetails()
			success(data)

			fillRunnersTable()
		} else {
			failure(data.message)
		}
	})
	.catch(err => {
		failure(err.message)
	})
}

// Check login
if (getToken()) {
	let form = new FormData()
	form.append('token', getToken())

	login(form, data => {
		if (data.status != "success") {
			loginModal.parentNode.classList.remove('invisible')
		}
	}, () => loginModal.parentNode.classList.remove('invisible'))
} else {
	loginModal.parentNode.classList.remove('invisible')
}


loginModal.addEventListener('submit', e => {
	e.preventDefault()

	let errorContainer = e.target.children[4]
	errorContainer.innerText = ""
	
	let form = new FormData()
	form.append('token', e.target.elements.token.value)

	login(form, () => {
		loginModal.parentNode.remove()
	}, err => errorContainer.innerText = err)
})


/**
 * Logout
 */
const logout = () => {
	removeToken()
	window.location.reload()
}
document.getElementById("logout").addEventListener('click', logout)


/**
 * Team details
 */
const saveTeamDetails = form => {
	console.log(form)
	apiRequest(`/teams/${user.teamId}`, 'PUT', {
		'name': form.teamName.value,
		'location': form.location.value,
		'contactEmail': form.contactEmail.value
	}, data => {}, err => console.error(err))

	document.getElementById('saveTeamDetails').disabled = true
}

const deleteModal = document.getElementById('deleteModal')
deleteModal.addEventListener("submit", e => {
	e.preventDefault()

	if (e.submitter.dataset.submitter == "delete") {
		console.log(2)
		apiRequest(`/teams/${user.teamId}`, 'DELETE', null, () => {
			logout()
		})
	} else {
		deleteModal.parentNode.classList.add('invisible')
	}

})

document.getElementById("teamDetails").addEventListener("submit", e => {
	e.preventDefault()

	if (e.submitter.dataset.submitter == "save") saveTeamDetails(e.target.elements)
	else if (e.submitter.dataset.submitter == "delete") {
		deleteModal.parentNode.classList.remove('invisible')
	}
})

for (let detail of document.getElementsByClassName('teamDetails')) {
	detail.addEventListener('change', () => {
		document.getElementById('saveTeamDetails').disabled = false
	})
}

/**
 * Runners table
 */
const tableBody = document.getElementById('runners')

const updateTable = (data) => {
	tableBody.innerHTML += data

	for (let action of document.getElementsByClassName('actionListener')) {
		action.children[0].addEventListener('click', e => {handleActions('copy', e.target.parentNode.parentNode)})
		action.children[1].addEventListener('click', e => {handleActions('save', e.target.parentNode.parentNode)})
		action.children[2].addEventListener('click', e => {handleActions('delete', e.target.parentNode.parentNode)})
	}

	for (let input of document.getElementsByClassName('runnerTableData')) {
		input.addEventListener('change', e => {
			e.target.parentNode.parentNode.children[4].children[1].disabled = false
		})
	}
}

const fillRunnersTable = () => {
	apiRequest(`/teams/${user.teamId}/runners`, 'GET', null, data => {
		let temp = ""
		data.forEach(item => {
			temp += `<tr data-id=${item.id}>
			<td><input type='text' class='inpt runnerTableData' value='${item.firstName}' /></td>
			<td><input type='text' class='inpt runnerTableData' value='${item.lastName}' /></td>
			<td><input type='text' class='inpt runnerTableData' value='${item.speed}' /></td>
			<td>${item.token}</td>
			<td class='actions actionListener'><button class='btn'>Copy</button><button class='btn primary' disabled>Save</button><button class='btn danger'>Delete</button></td>
		</tr>`
		})
		updateTable(temp)

		document.getElementById('addNewRunner').addEventListener('click', e => {
			addNewRunner(e)
		})
	}, err => console.error(err))
}

const handleActions = (action, elements) => {
	switch(action) {
		case 'delete': 
			apiRequest(`/teams/${user.teamId}/runners/${elements.dataset.id}`, 'DELETE', null, () => {
				elements.remove()
			})
			break;

		case 'copy':
			navigator.clipboard.writeText(elements.children[3].children[0].value)
			break;

		case 'save':
			apiRequest(`/teams/${user.teamId}/runners/${elements.dataset.id}`, 'PUT', {
				firstName: elements.children[0].children[0].value,
				lastName: elements.children[1].children[0].value,
				speed: elements.children[2].children[0].value
			}, data => {
				elements.children[4].children[1].disabled = true
			}, err => console.error(err))
			break;
	}
}

const addNewRunner = e => {
	let data = {
		firstName: document.querySelector("input[name='newRunnerFirstName']").value,
		lastName: document.querySelector("input[name='newRunnerLastName']").value,
		speed: document.querySelector("input[name='newRunnerSpeed']").value,
	}

	if (data.firstName.trim().length < 1 || data.lastName.trim().length < 1 || data.speed.trim().length < 1 || tableBody.children.length > 9) return 

	apiRequest(`/teams/${user.teamId}/runners`, 'POST', data, data => {
		updateTable(`<tr data-id=${data.id}>
			<td><input type='text' class='inpt runnerTableData' value='${data.firstName}' /></td>
			<td><input type='text' class='inpt runnerTableData' value='${data.lastName}' /></td>
			<td><input type='text' class='inpt runnerTableData' value='${data.speed}' /></td>
			<td>${item.token}</td>
			<td class='actions actionListener'><button class='btn'>Copy</button><button class='btn primary' disabled>Save</button><button class='btn danger'>Delete</button></td>
		</tr>`)

		for (let item of document.getElementsByClassName('newRunner')) {
			item.value = ""
		}
	}, err => console.error(err))
}