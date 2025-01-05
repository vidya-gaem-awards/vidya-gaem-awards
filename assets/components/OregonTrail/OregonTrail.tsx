import React from 'react';
import {Button} from "react-bootstrap";
import Game from "./Game";

export default function OregonTrail() {
    const [start, setStart] = React.useState(false);

    return (
        <>
            <Game start={start} onDisconnect={() => setStart(false)} />

            <div className="text-center p-4">
                <Button size="lg" variant="primary" onClick={() => setStart(!start)}>{start ? 'Disconnect' : 'Connect'}</Button>
            </div>
        </>

    );
}
