function validateCustomPhone($value) {
    if($value === ""){
        return {
            'code': false,
            'message': 'Au moins 1 téléphone doit être renseigné.'
        };
    }
    if (!(/^((\+)33|0)[1-9](\d{2}){4}$/.test($value))){
        return {
            'code': false,
            'message': 'Ce numéro n\'est pas valide.'
        };
    }
    return {'code': true};
}

function validatePhone($value) {
    if($value === ""){
        return {
            'code': false,
            'message': 'Ce numéro n\'est pas valide.'
        };
    }
    if (!(/^((\+)33|0)[1-9](\d{2}){4}$/.test($value))){
        return {
            'code': false,
            'message': 'Ce numéro n\'est pas valide.'
        };
    }
    return {'code': true};
}

function validateDate($value) {
    if($value === ""){
        return {
            'code': false,
            'message': 'Ce champ doit être renseigné.'
        };
    }
    if($value.length !== 10){
        return {
            'code': false,
            'message': 'Cette date n\'est pas valide.'
        };
    }
    const compare = (a,b) => a.getTime() >= b.getTime();
    if(!compare(new Date(), new Date($value))){
        return {
            'code': false,
            'message': 'Cette date n\'est pas valide.'
        };
    }
    return {'code': true};
}

function validateText($value) {
    if($value === ""){
        return {
            'code': false,
            'message': 'Ce champ doit être renseigné.'
        };
    }
    return {'code': true};
}

function validateEmail($value){
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($value)){
        return {'code': true};
    }
    return {
        'code': false,
        'message': 'Cette adresse e-mail est invalide.'
    };
}

function validateur(values){
    let validate; let code = true;
    let errors = {};
    values.forEach(element => {
        switch (element.type) {
            case 'text':
                validate = validateText(element.value);
                break;
            case 'customPhone':
                validate = validateCustomPhone(element.value);
                break;
            case 'phone':
                validate = validatePhone(element.value);
                break;
            case 'date':
                validate = validateDate(element.value);
                break;
                break;
            case 'email':
                validate = validateEmail(element.value);
                break;
        }
        if(!validate.code){
            errors[element.id] = {
                value: element.value,
                error: validate.message
            };
            code = false;
        }
    });

    return {
        'code': code,
        'errors': errors
    };
}

module.exports = {
    validateur
}