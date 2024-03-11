/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { _x } from '@wordpress/i18n';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { STORE_KEY as CONTAINER_STORE_KEY } from './constants';
import './style.scss';

import StatusUI from './components/status-ui';
import AddToCartButton from './components/add-to-cart-button';
import CancelButton from './components/cancel-button';
import './filters';

const MobileFooter = () => {
    // Track all props in state. This is a bit of a hack to get around the fact that we can't use useSelect for simple mix and match yet.
    const [ stateProps, setStateProps ] = useState(
        {
            container: null,
            context: 'add-to-cart',
            maxContainerSize: '',
            messages: [],
            minContainerSize: 0,
            passesValidation: false,
            totalPrice: 0,
            totalQuantity: 0,
        } 
    );

    const containerStoreExists = useSelect(
        ( select ) => {
        return select(CONTAINER_STORE_KEY) !== undefined;
        }, [] 
    );

    // If the store exists, get the status from the container data store. For now, this means we are dealing with variable mix and match.

    // Get the status from the container data store.
    const storeProps = useSelect(
        ( select ) => {
        if (containerStoreExists ) {
            return {
                container: select(CONTAINER_STORE_KEY).getContainer(),
                context: select(CONTAINER_STORE_KEY).getContext(),
                maxContainerSize:
                select(CONTAINER_STORE_KEY).getMaxContainerSize(),
                messages: select(CONTAINER_STORE_KEY).passesValidation()
                ? select(CONTAINER_STORE_KEY).getMessages('status')
                : select(CONTAINER_STORE_KEY).getMessages('errors'),
                minContainerSize:
                select(CONTAINER_STORE_KEY).getMinContainerSize(),
                passesValidation:
                select(CONTAINER_STORE_KEY).passesValidation(),
                totalPrice: select(CONTAINER_STORE_KEY).getTotal(),
                totalQuantity: select(CONTAINER_STORE_KEY).getTotalQuantity(),
            };
        } else {
        return {};
        }
        }, [] 
    );

    // Detect a container change/definition. Certain props only change this one time.
    useEffect(
        () => {
            if (stateProps.container?.id !== storeProps.container?.id ) {
                setStateProps(
                    ( prevState ) => {
                    return {
                             ...prevState,
                             ...{
                                    container: storeProps.container,
                                    context: storeProps.context,
                                    minContainerSize: storeProps.minContainerSize,
                                    maxContainerSize: storeProps.maxContainerSize,
                            },
                        };
                    } 
                );
            }
        }, [ storeProps.container ] 
    );

    // Variable Mix and Match should use useSelect with the data store, but an event listener will work for both until simple MNM gets a data store too.
    useEffect(
        () => {
        const updateUI = ( event ) => {
                setStateProps(
            ( prevState ) => {
                    return {
                             ...prevState,
                             ...{
                                    passesValidation: event.detail.passesValidation,
                                    messages: event.detail.passesValidation
                                    ? event.detail.messages.status
                                    : event.detail.messages.errors,
                                    totalPrice: event.detail.total,
                                    totalQuantity: event.detail.totalQuantity,
                                },
                    };
            } 
        );
        };
        const form = document.querySelector(`form.mnm_form`);
        if (form ) {
            form.addEventListener(
            'wc/mnm/container/container-updated',
            updateUI
            );
        }
        // For simple Mix and Match, there's no data store yet and we need to get data from the REST responset.
            if (! containerStoreExists && form ) {
                const containerId = form.getAttribute('data-container_id');

                const container = apiFetch(
                    {
                        path: `/wc/store/v1/products/${ containerId }`,
                    } 
                )
                .then(
                    ( container ) => {
                        if (container && container.id ) {
                            const minContainerSize =
                            container?.extensions?.mix_and_match
                            ?.min_container_size ?? 0;
                            const maxContainerSize =
                            container?.extensions?.mix_and_match
                            ?.max_container_size ?? '';
                            const context =
                            form.getAttribute('data-validation_context') ??
                            'add-to-cart';

                            setStateProps(
                                ( prevState ) => {
                                return {
                                        ...prevState,
                                        ...{
                                            container: container,
                                            context: context,
                                            minContainerSize: minContainerSize,
                                            maxContainerSize: maxContainerSize,
                                        },
                                    };
                                } 
                            );
                        }
                    } 
                )
                .catch(
                    ( error ) => {
                    console.debug('error', error);
                    } 
                );
            }
        }, [] 
    );

    // Pull out a few props that we need in this file.
    const { container, context, passesValidation } = stateProps;

    // Don't show anything until there's a container ID set.
if (! container ) {
    return;
}

    return (
        <div
            className={ `mnm-mobile-content mnm_cart alignwide context-${stateProps.context} ${
                passesValidation ? 'passes_validation' : 'fails_validation'
                }` }
        >
            <div className="column col-1">
                <StatusUI { ...stateProps } />
            </div>

            <div className="column col-2">
                <div className="mnm_button_wrap">
                    <AddToCartButton
                        container={ container }
                        passesValidation={ passesValidation }
                    />
                    <a
                        href="#wc-mnm-child-items"
                        className="screen-reader-text"
                    >
                        { _x(
                            'Return to selections',
                            '[Frontend]',
                            'wc-mnm-mobile-styles'
                        ) }
                    </a>
                    <CancelButton context={ context } />
                </div>
            </div>
        </div>
    );
};

export default MobileFooter;
