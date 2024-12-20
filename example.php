<html>
    <head>

        <style>
            *{
                margin: 0;
                padding: 0;
            }

            body {
                background-color: lightgrey;
                overflow: hidden;
                width: 100%;
                height: 100%;
            }

            .container
            {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                flex-direction: column;
            }

            #nav{
                height: 30px;
            }

            #iframe{
                display: block;
                width: 600px;
                height: 450px;
                border:none;
            }

            label {
                margin: 0 5px;
            }

            select {
                margin: 0 5px;
            }
        </style>

        <script type="text/javascript">

            let API_3DVUE = {
                EVENT:{
                    LOAD_COMPLETE:"LOAD_COMPLETE",
                    MODEL_DATA:"MODEL_DATA"
                },
                ACTION:{
                    GET_MODEL_DATA:"GET_MODEL_DATA",
                    CHANGE_MATERIAL:"CHANGE_MATERIAL",
                    START_RENDERER:"START_RENDERER",
                    PAUSE_RENDERER:"PAUSE_RENDERER",
                    RESET_CAMERA:"RESET_CAMERA"
                }
            }





            //on attend que le document soit prêt
            document.addEventListener("DOMContentLoaded", () => {

                //on récupère l'iframe
                const iframeWindow = document.getElementById("iframe").contentWindow;

                //on crée une variable qui contiendra les infos de l'objet3D.
                let nav_data = null;
                let current_model_data = null;

                //on ajoute un écouteur pour recevoir les messages de l'iframe
                window.addEventListener("message", function(event) {

                    //on attend les message qui nous informe que le modèle 3D est prêt
                    if(event.data.event === API_3DVUE.EVENT.LOAD_COMPLETE)
                    {
                        //si on a pas encore les données de navigation
                        if(nav_data === null)
                        {
                            //on crée une demande des données
                            let data = {
                                action:API_3DVUE.ACTION.GET_MODEL_DATA,
                            }
                            iframeWindow.postMessage(data, "*");
                        }

                        //récupère les infos du modèle actuel pour afficher les éléments dans la navigation
                        current_model_data = JSON.parse(event.data.data);
                    }

                    //on attend les message qui nous informe que les données du modèle 3D sont prêtes
                    if(event.data.event === API_3DVUE.EVENT.MODEL_DATA)
                    {
                        let navElement = document.getElementById("nav");
                        //si on a déjà crée la nav on s'arrête ici
                        if(navElement.innerHTML.length > 0) return;

                        //on copie les infos de l'objet
                        nav_data = JSON.parse(event.data.data);
                        //pour chaque sous-objet
                        nav_data.model_data.mesh.forEach((mesh)=>{
                            //on crée un <label>
                            let labelElement = document.createElement("label");
                            //on ajoute le nom du sous-objet au label
                            labelElement.innerText = mesh.name;

                            //on crée un element <select>
                            let selectElement = document.createElement("select");
                            selectElement.setAttribute("mesh", mesh.id);

                            //pour chaque material possible pour le sous-objet
                            mesh.material.forEach((material)=>{
                                //on crée un element <option>
                                let optionElement = document.createElement("option");
                                //on ajoute son nom en contenu
                                optionElement.innerText = material.name;
                                //on set l'attribur value avec l'id du material utilisé par 3dvue
                                optionElement.setAttribute("value", material.id);

                                //si on retrouve l'id du material sur le mesh de l'objet en cours d'affichage
                                if(current_model_data.material_list[mesh.id] === material.id)
                                    //on dit que c'est lui qui est selectionné dans le menu
                                    optionElement.setAttribute("selected", "selected");

                                //on ajoute l'option à la selection
                                selectElement.append(optionElement);
                            });

                            //on ajoute un ecouteur sur la selection pour envoyer à 3dvue une demande changement de materiaux
                            selectElement.addEventListener("change", function() {

                                //on récupère l'id du mesh et celui du material
                                let mesh_id = this.getAttribute('mesh');
                                let mat_id = this.value;

                                //on envoi la demande à 3dvue
                                let data = {
                                    action:API_3DVUE.ACTION.CHANGE_MATERIAL,
                                    materials:{
                                        [mesh_id]:mat_id
                                    }
                                }

                                iframeWindow.postMessage(data, "*");
                            });

                            //on ajoute les éléments à la navigation
                            navElement.append(labelElement);
                            navElement.append(selectElement);
                        });
                    }
                });
            });

        </script>
    </head>

    <body>

        <div class="container">
            <div id="nav"></div>
            <iframe id='iframe' src="https://demov3.3dvue.fr/?p=030m0i05"/>
        </div>

    </body>
</html>

