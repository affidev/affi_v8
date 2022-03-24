import {FetchData, FormPrimaryButton} from '../components/Form.jsx'
import { useState } from 'preact/hooks'
import {Searchadress} from "./search/searchadress";
import { h } from "preact";

let route

export default function FormAdress(props){

    const [success, setSuccess] = useState(false)
    const [adresse, setAdresse] = useState("")
    const [propadresse, setPropadresse] = useState("")
    const [module, setModule]=useState(props.module)

    if(props.module==="_dispatch"){
        route='/customer/profil/adress/newadress';
    }else{
        route='/geolocate/op/newadress';
    }

    if (success) {
        return <Alert>adress ajoutée</Alert>
    }

    function handleChangeAdress(select){

        setPropadresse(select)
        setAdresse(select.label)
    }

    function onSuccess(response){
        setSuccess(response.success)
    }

    return (
        <div className="add-locate">
            <FetchData action={route} onSuccess={onSuccess} data={propadresse} id={props.id}>
                    <Searchadress onChangeAdress={handleChangeAdress} defaultValue=""/>
                    <div className='full'>
                        <FormPrimaryButton>soumettre</FormPrimaryButton>
                    </div>
            </FetchData>
        </div>
    )
}
