/**
 * WordPress dependencies
 */

import { useBlockProps , BlockControls } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { Disabled, ToolbarGroup } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render';
import { edit } from '@wordpress/icons';
import './asset/css/editor.scss';
import InputAPI from './components/inputApi';

const Edit = ( props ) => {
	const {
		attributes: { api_url },
		setAttributes,
	} = props;

	const blockProps = useBlockProps();

	const [ isInvalidURL, setIsInvalidURL ] = useState( api_url ? false : true );
	const [ isEmptyURL, setIsEmptyURL ] = useState( api_url ? false : true );
	const [ isLoading, setIsLoading ] = useState( api_url ? true : false );

	console.log( "api_url: " + api_url );
	
	const isValidURL = ( url ) => {
		const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
		  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
		  '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
		  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
		  '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
		  '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
	  
		return !!pattern.test( url );
	};

	const onChangeURL = ( newURL ) => {
		if ( ! newURL || newURL === "" ) {
			setIsEmptyURL(true);
		} else {
			setIsEmptyURL(false);
		}

		if ( ! isValidURL( newURL ) ) {
			setIsInvalidURL( true );
		} else {
			setIsInvalidURL( false );
		}
		setAttributes( { api_url: newURL } );
	};

	const handleClickUseURL = async () => {
		if ( isEmptyURL || isInvalidURL ) {
			return;
		}

        setIsLoading( true );
	};

	const toolbarControls = [
		{
			icon: edit,
			title: 'Edit URL',
			onClick: () => setIsLoading( false ),
		},
	];

	return (
			<div { ...blockProps }>
				<BlockControls>
					<ToolbarGroup controls={ toolbarControls } />
				</BlockControls>
				{ ! isLoading ? (
					<InputAPI 
						isInvalidURL = { isInvalidURL }
						isEmptyURL = { isEmptyURL }
						api_url = { api_url }
						isLoading = { isLoading }
						onChangeURL = { onChangeURL}
						handleClickUseURL = { handleClickUseURL }>
					</InputAPI>

				) : (
					<ServerSideRender
						block = "gutenberg-block/offers"
						attributes = { props.attributes }
						className = "wrapper_offers"
					/>
				) }
			</div>
	);
};

export default Edit;
