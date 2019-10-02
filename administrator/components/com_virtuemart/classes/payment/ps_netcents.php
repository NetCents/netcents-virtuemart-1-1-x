<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 *
 * @version ps_netcents
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright (C) 2019 netcents - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

/**
 * The ps_netcents class for processing netcents cryptocurrency payments
 */
class ps_netcents
{

    var $payment_code = "NETCENTS";
    var $classname = "ps_netcents";

    /**
     * Show all configuration parameters for this payment method
     * @returns boolean False when the Payment method has no configration
     */
    function show_configuration()
    {
        global $VM_LANG;
        $database = new ps_DB();
        /** Read current Configuration ***/
        require_once(CLASSPATH . "payment/" . $this->classname . ".cfg.php");
        ?>
        <script type="text/javascript">
            function CopyAndPaste(from, to) {
                document.getElementsByName(to)[0].value = document.getElementsByName(from)[0].value;
            }
        </script>
        <table>
            <tr>
                <td><strong>NetCents API Key</strong></td>
                <td>
                    <input type="text" name="NETCENTS_API_KEY" class="inputbox" value="<?php echo NETCENTS_API_KEY ?>" />
                </td>
                <td>Your API key found in your merchant account</td>
            </tr>
            <tr>
                <td><strong>NetCents Secret Key</strong></td>
                <td>
                    <input type="text" name="NETCENTS_SECRET_KEY" class="inputbox" value="<?php echo NETCENTS_SECRET_KEY ?>" />
                </td>
                <td>Your Secret key found in your merchant account</td>
            </tr>
            <tr>
                <td><strong>NetCents Hosted Payment ID</strong></td>
                <td>
                    <input type="text" name="NETCENTS_HOSTED_PAYMENT_ID" class="inputbox" value="<?php echo NETCENTS_HOSTED_PAYMENT_ID ?>" />
                </td>
                <td>Hosted Payment ID that you created in your merchant account</td>
            </tr>
            <tr>
                <td><strong>Gateway URL</strong></td>
                <td>
                    <input type="text" name="NETCENTS_GATEWAY" class="inputbox" value="<?php echo NETCENTS_GATEWAY ?>" />
                </td>
                <td>Leave this alone unless told otherwise.</td>
            </tr>
        </table>
<?php
        // return false if there's no configuration
        return true;
    }

    function has_configuration()
    {
        // return false if there's no configuration
        return true;
    }

    /**
     * Returns the "is_writeable" status of the configuration file
     * @param void
     * @returns boolean True when the configuration file is writeable, false when not
     */
    function configfile_writeable()
    {
        return is_writeable(CLASSPATH . "payment/" . $this->classname . ".cfg.php");
    }

    /**
     * Returns the "is_readable" status of the configuration file
     * @param void
     * @returns boolean True when the configuration file is writeable, false when not
     */
    function configfile_readable()
    {
        return is_readable(CLASSPATH . "payment/" . $this->classname . ".cfg.php");
    }
    /**
     * Writes the configuration file for this payment method
     * @param array An array of objects
     * @returns boolean True when writing was successful
     */
    function write_configuration(&$d)
    {

        $my_config_array = array(
            "NETCENTS_API_KEY" => $d['NETCENTS_API_KEY'],
            "NETCENTS_SECRET_KEY" => $d['NETCENTS_SECRET_KEY'],
            "NETCENTS_GATEWAY" => $d['NETCENTS_GATEWAY'],
            "NETCENTS_HOSTED_PAYMENT_ID" => $d['NETCENTS_HOSTED_PAYMENT_ID'],
        );
        $config = "<?php\n";
        $config .= "if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); \n\n";
        foreach ($my_config_array as $key => $value) {
            $config .= "define ('$key', '$value');\n";
        }

        $config .= "?>";

        if ($fp = fopen(CLASSPATH . "payment/" . $this->classname . ".cfg.php", "w")) {
            fputs($fp, $config, strlen($config));
            fclose($fp);
            return true;
        } else
            return false;
    }

    /**************************************************************************
     ** name: process_payment()
     ** created by: soeren
     ** description: 
     ** parameters: $order_number, the number of the order, we're processing here
     **            $order_total, the total $ of the order
     ** returns: 
     ***************************************************************************/
    function process_payment($order_number, $order_total, &$d)
    {
        return true;
    }
}
