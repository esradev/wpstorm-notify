import React from 'react';
import {__} from "@wordpress/i18n";

const Footer = () => {
    return (
        <footer className="border-top text-center small text-muted py-3 container">
            <p>
                <a className="mx-1" href="https://wpstorm.ir/about/" target="_blank">
                    {__("About Us", "wpstorm-notify")}
                </a>{" "}
                |{" "}
                <a className="mx-1" href="https://wpstorm.ir/faq/" target="_blank">
                    {__("Terms", "wpstorm-notify")}
                </a>
            </p>
            <p className="m-0">
                {__("Copyright", "wpstorm-notify")} &copy; {__("2022", "wpstorm-notify")}{" "}
                <a href="https://wpstorm.ir/" target="_blank" className="text-muted">
                    {__("Wpstorm", "wpstorm-notify")}
                </a>
                {__(". All rights reserved.", "wpstorm-notify")}
            </p>
        </footer>
    );
};

export default Footer;