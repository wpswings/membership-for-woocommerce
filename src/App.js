import {useState} from 'react';
import { makeStyles } from '@material-ui/core/styles';
import { Button, Typography, Container, CircularProgress} from '@material-ui/core';
import Stepper from './component/Stepper';
import SecondStep from './component/SecondStep';
import ThirdStep from './component/ThirdStep';
import FinalStep from './component/FinalStep';
import Context from './store/store';
import axios from 'axios';
import { useTranslation } from 'react-i18next';
import { __ } from '@wordpress/i18n';
import qs from 'qs';
const useStyles = makeStyles((theme) => ({
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
    },
}));

function App(props) {
    const [loading, setLoading] = useState(false);
    const [state, setState] = useState({
        firstName: '',
        email: '',
        desc:'',
        licenseCode: '',
        licenseData: '',
        age: '',
        FirstCheckbox:false,
        checkedA: false,
        checkedB: false,
        consetCheck:'yes',
        memPlanAmount:'',
        memPlanTitle:'',
        memPlanProduct:'',        
    });
    const { t, i18n } = useTranslation();

    const classes = useStyles();
    const [activeStep, setActiveStep] = useState(0);
    const steps = [ __( 'General Setting', 'membership-for-woocommerce' ), __( 'Membership Creation', 'membership-for-woocommerce' ), __( 'Final Step', 'membership-for-woocommerce' )];

    
    const onFormFieldHandler = (event) => {
        let value = ('checkbox' === event.target.type ) ? event.target.checked : event.target.value;
        setState({ ...state, [event.target.name]: value });
    };
    const getStepContent = (stepIndex) => {
        switch (stepIndex) {
            case 0:
                return <ThirdStep />;
            case 1:
                return (<SecondStep/>);   
           
            case 2:
            return <FinalStep />;
            case 3:
                return <h1>{__( 'Thanks for your details', 'membership-for-woocommerce' )}</h1>;
            default:
                return __( 'Unknown stepIndex', 'membership-for-woocommerce' );
        }
    }
    const handleNext = () => {
        setActiveStep((prevActiveStep) => prevActiveStep + 1);
    };

    const handleBack = () => {
        setActiveStep((prevActiveStep) => prevActiveStep - 1);
    };
    const[error,setError] = useState('');
    const[success,setSuccess] = useState('');
    // const handleFormSubmit = (e) => {
    //     e.preventDefault();
    //     setLoading(true);
    //  let val =   buttonclickhandler();
    //  console.log( val );
    //  if (val.data.status == true) {
    //      setSuccess(res);
    //      savedatakhandler();
           
    //  } else {
    //      setError(res);
    //      savedatakhandler();
       
    //  }



      
        
    // }

    const savedatakhandler = (e) => {
        const user = {
            ...state,
            'action': 'mwb_standard_save_settings_filter',
            nonce: frontend_ajax_object.mwb_standard_nonce,   // pass the nonce here
        };
        
        axios.post(frontend_ajax_object.ajaxurl, qs.stringify(user) )
            .then(res => {
                setLoading(false);
                console.log( res.data);
                handleNext();
                setTimeout(() => {
               //   window.location.href = frontend_ajax_object.redirect_url; 
                    return null;
                }, 3000);
            }).catch(error=>{
                console.log(error);
        })
    }

    const handleFormSubmit = (e) => {

       // let LicenceCode = ctx.formFields['licenseCode'];
        setError('');
        setSuccess('');
        const user = {
            ...state,
            'action': 'mfwp_membership_validate_license_key',
            nonce: frontend_ajax_object.mwb_standard_nonce,   // pass the nonce here
        };
        axios.post(frontend_ajax_object.ajaxurl, qs.stringify(user) )
        .then(res => {
           
            console.log( res.data );
            if (res.data.status == true) {
                setSuccess(res.data.msg);
                setState({ ...state, ['licenseData']: res.data.msg });
            } else {
                setError(res.data.msg);
                setState({ ...state, ['licenseData']: res.data.msg });
                savedatakhandler();
            }
        }).catch(er=>{
            setError(er);
            console.log(er);
    })
    }

    let nextButton = (
        <Button
            variant="contained" color="primary" onClick={handleNext} size="large">
            Next
        </Button>
    );
    if (activeStep === steps.length-1 ) {
        nextButton = (
            <Button
                onClick={handleFormSubmit}
                variant="contained" color="primary" size="large">
                Finish
            </Button>
        )
    } 
    return (
        <Context.Provider value={{
            formFields:state,
            changeHandler:  onFormFieldHandler, 
        }}>
            <div className="mwbMsfWrapper">
                <Stepper activeStep={activeStep} steps={steps}/>
                <div className="mwbHeadingWrap">
                    <h2>{t('Welcome to Makewebbetter')}</h2>
                    <p>{t('Complete The steps to get started','membership-for-woocommerce') }</p>
                </div>
                <Container maxWidth="sm">
                    <form className="mwbMsf">
                        <Typography className={classes.instructions}>
                            {(loading) ? <CircularProgress className="mwbCircularProgress" /> :getStepContent(activeStep)}
                        </Typography>
                        <div className="mwbButtonWrap">
                            {activeStep !== steps.length && <Button
                                disabled={activeStep === 0}
                                onClick={handleBack}
                                variant="contained" size="large">
                            Back
                            </Button>}
                            {activeStep !== steps.length && nextButton}
                        </div>
                    </form>
                </Container >
            </div>
        </Context.Provider>
    );
}

export default App;

