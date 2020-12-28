import React from 'react';

export interface PhotographProps {
    name: string;
    image: string;
    onClick?(): any;
}

class Photograph extends React.Component<PhotographProps> {
    render() {
        return (
            <div className="photograph" onClick={this.props.onClick}>
                <div className="photo-image" style={{ backgroundImage: this.props.image ? `url("${this.props.image}")` : null }} />
                <div className="photo-text">{this.props.name}</div>
                <div className="photo-bg" />
            </div>
        )
    }
}

export default Photograph;
