import React,{useContext} from 'react';
import { Select, FormGroup, InputLabel, MenuItem, Checkbox, FormControlLabel, FormControl, TextField } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { __ } from '@wordpress/i18n';
import Context from '../store/store';
const useStyles = makeStyles({
      margin: {
        marginBottom: '20px',
      },
});
const SecondStep = (props) => {
    const classes = useStyles();
    const ctx = useContext(Context);
    let menuItems = [];
    for( let key in frontend_ajax_object.products_list ) {
      let singleItem = (
        <MenuItem key={key} value={key}>{ frontend_ajax_object.products_list[key] }</MenuItem>
      )
      menuItems.push(singleItem);
    }

    return ( 
    <>
          <h3 className="wps-title">{ __( 'Membership plan Creation', 'membership-for-woocommerce' ) }</h3>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">

                <TextField 
                 value={ctx.formFields['memPlanTitle']}
                 onChange={ctx.changeHandler} 
                 id="memPlanTitle" 
                 name="memPlanTitle" 
                 label="Membership Plan Name"  variant="outlined" className={classes.margin}/>

                <TextField 
                  value={ctx.formFields['memPlanAmount']}
                  onChange={ctx.changeHandler} 
                  id="memPlanAmount" 
                  name="memPlanAmount" 
                  label="Membership Plan Amount"  variant="outlined" className={classes.margin}/>

                <FormControl component="fieldset" variant="outlined" fullWidth className="fieldsetWrapper">
                   <InputLabel  className={classes.margin} id="demo-simple-select-outlined-label">{__('Include Product in Membership','membership-for-woocommerce') }</InputLabel>
                   <Select
                    labelId="demo-simple-select-outlined-label"
                    name="memPlanProduct"
                    id="demo-simple-select-outlined"
                    value={ctx.formFields['memPlanProduct']}
                    onChange={ctx.changeHandler}
                    class="wc-membership-product-tag-search"
                    label="Include Product in Membership"
                    className={classes.margin}>
                    {menuItems}
                   </Select>
                </FormControl>
            </FormControl>
    </>
    )
}
export default SecondStep;