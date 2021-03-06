import {useCallback, useEffect, useRef, useState} from "preact/hooks";
import {useJsonFetchOrFlash} from "../../functions/hooks";
import {debounce} from "../../functions/timers";
import {Loader} from "../../components/Loader";
import {classNames} from "../../functions/dom";
import { h, render} from "preact";

const SEARCH_API = '/ajx/method/find/searchnocity'

export function SearchPartner ({ defaultValue, onChangePartner}) {
    const input = useRef( null)
    const [query, setQuery] = useState(defaultValue)
    const { loading, fetch, data } = useJsonFetchOrFlash()
    const [selectedItem, setSelectedItem] = useState(null)
    const [selecteInput, setSelecteInput] = useState(false)
    let results =[];
    if(selecteInput){
       results = data?.items || []
    }
    if (query === '') {
        results = []
    }
    const hits = data?.hits || 0
    if (query !== '' && results.length === 0) {
        results = [
            ...results,
            {
                title: ``,
                url: ``,
                pict:''
            }
        ]
    }
    const suggest = useCallback(
        debounce(async e => {
            await fetch(`${SEARCH_API}?q=${encodeURI(e.target.value)}`)
            setSelectedItem(null)
        }, 300),
        []
    )
    const onInput = e => {
        setSelecteInput(true)
        setQuery(e.target.value)
        suggest(e)
    }
    const handleselect=(r,e) =>{
        e.preventDefault()
        setSelecteInput(false)
        setQuery(r.title)
        onChangePartner(r)
    }
    const moveFocus = useCallback(
        direction => {
            if (results.length === 0) {
                return
            }
            setSelectedItem(i => {
                const newPosition = i + direction
                if (i === null && direction === 1) {
                    return 0
                }
                if (i === null && direction === -1) {
                    return results.length - 1
                }
                if (newPosition < 0 || newPosition >= results.length) {
                    return null
                }
                return newPosition
            })
        },
        [results]
    )
    useEffect(() => {
        const handler = e => {
            switch (e.key) {
                case 'ArrowDown':
                case 'Tab':
                    e.preventDefault()
                    moveFocus(1)
                    return
                case 'ArrowUp':
                    moveFocus(-1)
                    break
                default:
            }
        }
        window.addEventListener('keydown', handler)
        return () => window.removeEventListener('keydown', handler)
    }, [moveFocus])
    useEffect(() => {
        input.current.focus()
    }, [])
    return (
        <div className='search-input_board'>
            <input
                autoFocus
                type='search'
                name='q'
                ref={input}
                onInput={onInput}
                autoComplete='off'
                value={query}
                placeholder='Recherchez un panneau...'
            />
            {loading && <Loader class='search-input_loader' />}
            {(results.length > 0  && selecteInput) &&
                <ul className='search-input_suggestions'>
                    {results.map((r, index) => (
                        <li key={r.url} >
                            <div class={classNames(index === selectedItem && 'focused')}  onClick={(e)=>handleselect(r,e)}>
                                <img class='imgsearch' src={r.pict}/>
                                <span dangerouslySetInnerHTML={{ __html: r.title }} />
                            </div>
                        </li>
                    ))}
                </ul>
            }
        </div>
    )
}