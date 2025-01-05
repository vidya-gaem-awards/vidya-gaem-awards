import React, {useEffect} from "react";
import {DateTime} from 'luxon';

interface Props {
    start: boolean;
    onDisconnect: () => void;
}

interface GameData {
    day: number;
    location: string;
    nextActionTime: string;
    party: string[];
    players: number;
}

export default function Game({start, onDisconnect}: Props) {
    const [lastMessage, setLastMessage] = React.useState('Not connected');
    const [eventSource, setEventSource] = React.useState<EventSource | null>(null);

    const [gameData, setGameData] = React.useState<GameData | null>(null);
    const [useEventSource, setUseEventSource] = React.useState(false);

    useEffect(() => {
        let cancelled = false;

        if (!start) {
            setUseEventSource(false);
            setGameData(null);
            return;
        }

        setLastMessage('Loading game data...');

        fetch(window.gameConfig.urls.gameData).then(result => {
            if (cancelled) {
                return;
            }

            return result.json();
        }).then(data => {
            if (!data) {
                return;
            }
            setGameData(data);
            setLastMessage('Game data loaded')
            setUseEventSource(true);
        });

        return () => {
            cancelled = true;
        };
    }, [start]);

    useEffect(() => {
        if (!useEventSource) {
            if (eventSource) {
                eventSource.close();
            }
            return;
        }

        setLastMessage('Connecting...');

        const source = new EventSource(window.gameConfig.urls.mercure)
        setEventSource(source);
        source.onmessage = event => {
            // Will be called every time an update is published by the server
            console.log(JSON.parse(event.data));
            setLastMessage('Last message: ' + event.data);
        };

        source.onopen = () => {
            setLastMessage('Connected, no messages yet');
            console.log('Connection established');
        };

        source.onerror = error => {
            setLastMessage('Connection error');
            console.error('EventSource failed:', error);
        };

        return () => {
            setLastMessage('Connection closed');
            console.log('Connection closed');
            source.close();
        }
    }, [useEventSource]);

    return (
        <div className="text-center p-4 oregon-trail">
            <div className="console-window">
                {lastMessage}
            </div>
            {gameData &&
            <div className="bar">
                <div className="bar-element">Day {gameData.day}</div>
                <div className="bar-element">Next action: {DateTime.fromISO(gameData.nextActionTime).toRelative({unit: 'second'})}</div>
                <div className="bar-element">{gameData.players} active players</div>
            </div>}
        </div>
    );
}
