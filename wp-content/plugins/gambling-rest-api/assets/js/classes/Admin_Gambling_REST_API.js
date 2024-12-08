export default class Admin_Gambling_REST_API {

    constructor() {
        this.submitForm()

    }

    /**
     * submitForm function is used for submitting data from plugin page, with this plugin we assure not to refresh inside of dashboard
    */

    submitForm = (pointer) => {
        console.log('clicked');
        if (pointer === 'users') {
            let form = document.getElementById('gambingFormSeed')
            let url = form.action
            let seed = form.elements['seed'].checked
            let users = true
            let admin_DB_seed_nonce = form.elements['_wpnonce'].value

            let loadingDialog = document.querySelector('#loadingDialog')

            loadingDialog.showModal()


            let data = new URLSearchParams({
                'action': 'admin_seed_submit',
                users,
                seed,
                admin_DB_seed_nonce
            })

            loadingDialog.showModal()

            fetch(url, {
                method: 'POST',
                body: data,
            }).then(
                response => response.text()
            ).then(data => {
                console.log(data)
                loadingDialog.close()
                //this.submitForm('users')

            }).catch((error) => {
                console.error('Error:', error)
            }).finally(() => {
                loadingDialog.close()
            })
        } else {
            document.querySelector('#seedButton').addEventListener('click', (event) => {
                console.log('clicked');

                // Prevent the default form submission
                event.preventDefault();

                let form = document.getElementById('gambingFormSeed')
                let url = form.action
                let seed = form.elements['seed'].checked
                let admin_DB_seed_nonce = form.elements['_wpnonce'].value

                let loadingDialog = document.querySelector('#loadingDialog')

                loadingDialog.showModal()


                let data = new URLSearchParams({
                    'action': 'admin_seed_submit',
                    seed,
                    admin_DB_seed_nonce
                })

                loadingDialog.showModal()

                fetch(url, {
                    method: 'POST',
                    body: data,
                }).then(
                    response => response.text()
                ).then(data => {
                    console.log(data)

                    this.submitForm('users')

                }).catch((error) => {
                    console.error('Error:', error)
                })

            })
        }



    }



}