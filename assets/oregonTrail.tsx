import React from 'react';
import { createRoot } from 'react-dom/client';
import OregonTrail from "./components/OregonTrail/OregonTrail";
import './styles/oregonTrail.scss';

const root = createRoot(document.getElementById('oregonTrail') as HTMLElement);

root.render(
    <React.StrictMode>
        <div className="center-container mb-4">
            <div className="bottomContainer">
                <div className="inventory-title-area">
                    <div className="title-text">
                        /v/ Plays The Oregon Trail
                    </div>
                </div>

                <OregonTrail />

                <div className="plank-background">
                    <div className="plank-inner-border"></div>
                </div>
            </div>
        </div>
    </React.StrictMode>
)
