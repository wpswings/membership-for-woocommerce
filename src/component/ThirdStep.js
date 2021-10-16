import React,{useContext} from 'react';
import Context from '../store/store';
import { Switch, FormGroup, FormControlLabel} from '@material-ui/core';
import { __ } from '@wordpress/i18n';
import { makeStyles } from '@material-ui/core/styles';
const useStyles = makeStyles({
      margin: {
        marginBottom: '20px',
      },
});
const ThirdStep = (props) => {
    const classes = useStyles();
    const ctx = useContext(Context);
    
    return ( 
    <>
    <h3 className="mwb-title">{__( 'Setting', 'membership-for-woocommerce' ) }</h3>
    <FormGroup>
        <FormControlLabel
            control={
            <Switch
                checked={ctx.formFields['checkedA']}
                onChange={ctx.changeHandler}
                name="checkedA"
                color="primary"
                 />
            }
            label="General Settings"
            className={classes.margin} />
        <FormControlLabel
            control={
            <Switch
                checked={ctx.formFields['checkedB']}
                onChange={ctx.changeHandler}
                name="checkedB"
                color="primary"
               />
            }
            label="Reset License"
            className={classes.margin} />
        </FormGroup>
    </>
    )
}
export default ThirdStep;