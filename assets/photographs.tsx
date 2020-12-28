import React from 'react'
import ReactDOM from 'react-dom'
import Photographs from './pages/Photographs'

import './styles/photographs.scss';
import {Advertisement} from "./entities";

declare var decorations: Map<number, Advertisement>

ReactDOM.render(<Photographs decorations={Object.values(decorations)} />, document.getElementById('react-photographs'));
