// This file should be kept in sync with any Entity classes in PHP that have a JSON representation.

export interface Advertisement {
    id: number|null,
    name: string,
    link: string|null,
    image: File|null,
    special: boolean
}

export interface CaptchaGame {
    id: number|null,
    title: string,
    first: string,
    second: string,
    image: File|null
}

export interface File {
    fullFilename: string,
    relativePath: string,
    url: string
}
