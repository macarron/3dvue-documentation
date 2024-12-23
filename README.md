<p align="center">
  <img src="https://demo.3dvue.fr/project/logo.png" width="250">
</p>

# 3dvue.fr
3dvue.fr est une plateforme web de visualisation de modèle 3D en ligne.
3dvue peut-être embarquée dans votre site internet sous forme d'iframe afin de s'intégrer complètement dans votre environnement.
Une API est également disponible afin de manipuler l'iframe et ajouter une navigation depuis votre site pour configurer le modèle 3D.

## Utilisation

### Basique
Exemple basic d'utilisation d'une iframe pour visualiser un modèle 3D
```
<iframe id='iframe' src="https://[votre-domaine-3dvue].3dvue.fr/?p=[objet-cible]"/>
```
L'iframe est ensuite paramétrable via CSS comme n'importe qu'elle balise. La camera s'adaptera en fonction de la taille disponible.

### Avancé
Utilisation avancée de 3dvue.fr via Javascript. 

#### Avant de commencer
Pour exectuer une action dans l'iframe comme changer une texture de l'objet on utilisera la methode `postMessage`

L'iframe pourra aussi nous envoyer des informations sous forme d'évènement que l'on recevera via un écouteur `window.addEventListener("message", function(event) {});`

La liste des actions et des évènements est définit ci-dessous, vous pouvez copier cet objet dans votre code javascript pour simplifer les requêtes
```
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
```

#### API - ACTION
```
API_3DVUE.ACTION.GET_MODEL_DATA
```
Demande à l'iframe de renvoyer les données du model affiché. Il faut attendre que le modèle soit chargé pour effectuer cette requête.

```
//quand la page est chargée
document.addEventListener("DOMContentLoaded", () => {
  //on écoute si un "message" arrive
  window.addEventListener("message", function(event) {
    //si le type de message est API_3DVUE.EVENT.LOAD_COMPLETE...
    if(event.data.event === API_3DVUE.EVENT.LOAD_COMPLETE) {
      let data = {
          action:API_3DVUE.ACTION.GET_MODEL_DATA,
      }
      //récupère l'element html de l'iframe par son id
      const iframeWindow = document.getElementById("iframe").contentWindow;
      //envoi la requête
      iframeWindow.postMessage(data, "*");
    }
  });
});
```


```
API_3DVUE.ACTION.CHANGE_MATERIAL
```
Effectue une requête pour changer dynamiquement les materiaux de l'objet affiché.  Il faut attendre que le modèle soit chargé pour effectuer cette requête.

```
//quand la page est chargée
document.addEventListener("DOMContentLoaded", () => {
  //on écoute si un "message" arrive
  window.addEventListener("message", function(event) {
    //si le type de message est API_3DVUE.EVENT.LOAD_COMPLETE...
    if(event.data.event === API_3DVUE.EVENT.LOAD_COMPLETE) {
      let data = {
          action:API_3DVUE.ACTION.CHANGE_MATERIAL,
          materials:{
            "mesh_001":"mat_002",
            "mesh_002":"mat_002"
          }
      }
      //récupère l'element html de l'iframe par son id
      const iframeWindow = document.getElementById("iframe").contentWindow;
      //envoi la requête
      iframeWindow.postMessage(data, "*");
    }
  });
});
```

```
API_3DVUE.ACTION.START_RENDERER
```
Effectue une requête pour redémarrer le rendu après une pause.

```
API_3DVUE.ACTION.PAUSE_RENDERER
```
Effectue une requête pour arrêter le rendu.

```
API_3DVUE.ACTION.RESET_CAMERA
```
Effectue une requête pour remettre la camera à sa position d'origine.


#### API - EVENT
```
API_3DVUE.EVENT.MODEL_DATA
```
Signal de retour quand l'action GET_MODEL_DATA est executée.

```
//quand la page est chargée
document.addEventListener("DOMContentLoaded", () => {
  //on écoute si un "message" arrive
  window.addEventListener("message", function(event) {
    //si le type de message est API_3DVUE.EVENT.MODEL_DATA...
    if(event.data.event === API_3DVUE.EVENT.MODEL_DATA) {
      let data = JSON.parse(event.data.data);
      console.log(data);

      //toutes les données de l'objet nécessaire à la création d'une navigation.
      /*
        {
        "model_data": {
            "mesh": [
                {
                    "id": "mesh_001",
                    "name": "assise",
                    "material": [
                        {
                            "id": "mat_001",
                            "name": "noir"
                        },
                        {
                            "id": "mat_002",
                            "name": "bleu"
                        },
                        {
                            "id": "mat_003",
                            "name": "vert foncé"
                        }
                    ]
                },
                {
                    "id": "mesh_002",
                    "name": "dossier",
                    "material": [
                        {
                            "id": "mat_001",
                            "name": "noir"
                        },
                        {
                            "id": "mat_002",
                            "name": "bleu"
                        },
                        {
                            "id": "mat_003",
                            "name": "vert foncé"
                        }
                    ]
                }
            ]
        }
    }*/
    
    }
  });
});
```


```
API_3DVUE.EVENT.LOAD_COMPLETE
```
Signal que le model, le material ou les materiaux sont chargés. Il sera déclanché quand le modèle demandé en `src` de l'iframe est chargé ou quand une requête demandant un chargement est terminée.

```
//quand la page est chargée
document.addEventListener("DOMContentLoaded", () => {
  //on écoute si un "message" arrive
  window.addEventListener("message", function(event) {
    //si le type de message est API_3DVUE.EVENT.LOAD_COMPLETE...
    if(event.data.event === API_3DVUE.EVENT.LOAD_COMPLETE) {
      //la variable data contient des informations sur le modèle chargé, comme la liste des materiaux et son id
      let data = JSON.parse(event.data.data);
      console.log(data);

      //exemple d'objet reçu, le product_id peut être utilisé dans l'url de l'iframe pour charger l'objet directement
      /*
      {
        "material_list": {
            "mesh_001": "mat_001",
            "mesh_002": "mat_003",
            "mesh_003": "mat_006",
            "mesh_004": "mat_008"
        },
        "product_id": "0101030608"
      }
      */
    }
  });
});
```
