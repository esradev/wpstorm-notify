/**
 * Import remote dependencies.
 */
import React, {useState, useEffect, useContext} from "react";
import {__} from "@wordpress/i18n";

// Icons
import {TbMessageCircle2, TbPlus, TbSunHigh, TbUserCog, TbMessage2Bolt} from "react-icons/tb";


function Header() {
    const [credit, setCredit] = useState(0);
    /**
     * Get credit.
     *
     * @since 1.0.0
     */
    useEffect(() => {
        async function getCredit() {
            try {
                //wpstormNotifyJsObject is declared on class-wpstorm-notify-settings.php under admin_enqueue_scripts function
                const credit = await wpstormNotifyJsObject.getCredit;
                console.log(credit);
                setCredit(credit);
            } catch (e) {
                console.log(e);
            }
        }

        getCredit();
    }, []);

    return (
        <div className="app-header">
            <div className="app-header-left">
                <a href="#">
                    <div className="app-icon">
                        <TbMessage2Bolt />
                    </div>
                </a>
                <p className="app-name">{__("Wpstorm SMS", "wpstorm-notify")}</p>
                <div className="search-wrapper">
                    <input className="search-input" type="text" placeholder="Search"/>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         className="feather feather-search"
                         viewBox="0 0 24 24">
                        <defs></defs>
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg>
                </div>
            </div>
            <div className="app-header-right">
                <button className="mode-switch" title="Switch Theme">
                    <TbSunHigh/>
                </button>
                {/*<button className="add-btn" title="Add New Project">*/}
                {/*    <TbPlus/>*/}
                {/*</button>*/}
                {/*<button className="notification-btn">*/}
                {/*    <TbMessageCircle2/>*/}
                {/*</button>*/}
                <button className="profile-btn">
                    <TbUserCog/>
                    <span>Wpstorm Genius</span>
                </button>
            </div>
        </div>
    );
}

export default Header;
