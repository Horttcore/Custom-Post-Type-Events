import React from 'react';
import { useSelect } from '@wordpress/data';
import { __ } from "@wordpress/i18n";
import {
    Fragment
} from '@wordpress/element';
import {
    PanelBody,
    RangeControl,
    SelectControl,
    FormTokenField,
    Toolbar,
    __experimentalNumberControl as NumberControl
} from '@wordpress/components';
import { InspectorControls, BlockControls } from '@wordpress/block-editor';

const LoopControl = (props) => {
    const {
        setAttributes,
        order,
        orderBy,
        numberOfItems,
        orderByValues,
        offset,
        postIn,
        postType,
        showPostsMax,
        offsetMax,
        postLayout,
        gridColumns,
        gridColumnsMax,
        useGrid,
        page
    } = props;

    const { posts, postTaxonomies, postTypeData } = useSelect((select) => {
        const { getEntityRecords, getTaxonomy, getPostType } = select("core");
        // @ts-ignore
        const postTypeData = getPostType(postType);
        const postTaxonomiesData = [];
        let postsData = [];

        if (postTypeData && postTypeData.taxonomies) {
            postTypeData.taxonomies.map(tax => {
                // @ts-ignore
                const taxonomy = getTaxonomy(tax);
                if (taxonomy) {
                    // @ts-ignore
                    taxonomy.terms = getEntityRecords("taxonomy", tax, { per_page: 100 });
                    postTaxonomiesData.push(taxonomy);
                }
            });
        }

        // @ts-ignore
        postsData = getEntityRecords("postType", props.postType, {
            per_page: 100,
        });

        return {
            postTypeData,
            posts: postsData,
            postTaxonomies: postTaxonomiesData,
        };
    }, [props.postType]);

    const gridColumnsMaxValue = (gridColumnsMax) ? gridColumnsMax : 12;

    const layoutControls = [
        {
            icon: 'list-view',
            title: __('List View'),
            onClick: () => setAttributes({ postLayout: 'list', gridColumns: -1 }),
            isActive: postLayout === 'list',
        },
        {
            icon: 'grid-view',
            title: __('Grid View'),
            onClick: () => setAttributes({ postLayout: 'grid', gridColumns: 4 }),
            isActive: postLayout === 'grid',
        },
    ];

    const taxonomySelects = [];
    const orderByValue = [orderBy, order].join('/');
    const orderBySelectValues = (orderByValues) ? orderByValues : [
        {
            value: "title/asc",
            label: __("A → Z")
        },
        {
            value: "date/desc",
            label: __("Newest to Oldest")
        },
        {
            value: "date/asc",
            label: __("Oldest to Newest")
        },
        {
            value: "menu_order/asc",
            label: __("Menu order")
        },
    ];

    const showPostsMaxValue = (showPostsMax) ? showPostsMax : 100;

    const offsetMaxValue = (offsetMax) ? offsetMax : 100;

    const snakeToCamel = (str) => str.replace(
        /([-_][a-z])/g,
        (group) => group.toUpperCase()
            .replace('-', '')
            .replace('_', '')
    );

    if (postTaxonomies) {
        postTaxonomies.map((taxonomy, index) => {
            let termsFieldValue = [];
            if (taxonomy.terms !== null) {
                let selectedTerms = (props[snakeToCamel(taxonomy.slug)]) ? props[snakeToCamel(taxonomy.slug)] : [];
                termsFieldValue = selectedTerms.map((termId) => {
                    let wantedTerm = taxonomy.terms.find((term) => term.id === termId);
                    return (wantedTerm === undefined || !wantedTerm) ? false : wantedTerm.name;
                });
            }
            if (!taxonomy.terms) taxonomy.terms = [];
            taxonomySelects.push(
                <PanelBody
                    key={`taxonomy-${index}`}
                    title={taxonomy.name}
                >
                    <FormTokenField
                        value={termsFieldValue}
                        suggestions={taxonomy.terms.map(term => term.name)}
                        onChange={(selectedTerms) => {
                            let selectedTermsArray = [];
                            selectedTerms.map(
                                (termName) => {
                                    const matchingTerm = taxonomy.terms.find((term) => {
                                        return term.name === termName;

                                    });
                                    if (matchingTerm !== undefined) {
                                        selectedTermsArray.push(matchingTerm.id);
                                    }
                                }
                            )
                            let attr = [];
                            attr[snakeToCamel(taxonomy.slug)] = selectedTermsArray;
                            attr = { ...attr }
                            setAttributes(attr)
                        }}
                    />
                </PanelBody>
            );
        });
    }

    let postsFieldValue = [];
    let postNames = [];
    if (posts !== null && posts.length && postIn) {
        posts.map(post => {
            postNames.push(post.title.raw);
        })
        postsFieldValue = postIn.map((postId) => {
            let wantedPost = posts.find((post) => post.id === postId);
            return (wantedPost === undefined || !wantedPost) ? false : wantedPost.title.raw;
        });
    }
    return (
        <Fragment>
            {useGrid && (
                <BlockControls>
                    <Toolbar
                        // @ts-ignore
                        controls={layoutControls} />
                </BlockControls>
            )}
            <InspectorControls>
                {taxonomySelects}

                {(!postsFieldValue || postsFieldValue.length === 0) &&
                    <PanelBody title={__("Order")} initialOpen={false}>
                        <SelectControl
                            value={orderByValue}
                            onChange={orderByValue => {
                                const [orderBy, order] = orderByValue.split('/');
                                setAttributes({
                                    orderBy,
                                    order,
                                })
                            }}
                            options={orderBySelectValues}
                        />
                    </PanelBody>
                }

                <PanelBody title={__("Number of items")} initialOpen={false}>
                    <RangeControl
                        value={numberOfItems}
                        onChange={numberOfItems => { setAttributes({ numberOfItems }) }}
                        min={1}
                        max={showPostsMaxValue}
                        required
                    />
                    <NumberControl
                        label="Page"
                        value={page}
                        onChange={page => { setAttributes({ page }) }}
                    />
                </PanelBody>

                {postLayout === 'grid' && (
                    <PanelBody title={__("Grid columns")} initialOpen={false}>
                        <RangeControl
                            value={gridColumns}
                            onChange={gridColumns => { setAttributes({ gridColumns }) }}
                            min={1}
                            max={gridColumnsMaxValue}
                        />
                    </PanelBody>
                )}

                <PanelBody title={__("Offset")} initialOpen={ false }>
                    <RangeControl
                        value={offset}
                        onChange={offset => { setAttributes({ offset }) }}
                        min={0}
                        max={offsetMaxValue}
                        required
                    />
                </PanelBody>

                {posts && (
                    <PanelBody title={postTypeData.name} initialOpen={ false }>
                        <FormTokenField
                            label=""
                            value={postsFieldValue}
                            suggestions={postNames}
                            maxSuggestions={20}
                            onChange={(selectedPosts) => {
                                let selectedPostsArray = [];
                                selectedPosts.map(
                                    (postName) => {
                                        const matchingPost = posts.find((post) => {
                                            return post.title.raw === postName;

                                        });
                                        if (matchingPost !== undefined) {
                                            selectedPostsArray.push(matchingPost.id);
                                        }
                                    }
                                )
                                setAttributes({ postIn: selectedPostsArray });
                            }}
                        />
                    </PanelBody>
                )}
            </InspectorControls>
        </Fragment>
    );
}

export default LoopControl;