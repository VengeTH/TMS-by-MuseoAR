const sendData = async (password, id) => {
	let formData = new FormData()
	const url = "/api/email"
	formData.append("password", password)
	formData.append("id", id)
	const res = await fetch(url, {
		method: "POST",
		body: formData,
	}).then((res) => res.json())
	console.log(res)
}

//
